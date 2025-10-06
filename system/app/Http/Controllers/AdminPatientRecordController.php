<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Test;

class AdminPatientRecordController extends Controller
{
	public function details(Request $request, $id)
	{
		$path = storage_path('app/patient_records.json');
		$record = null;
		if (file_exists($path)) {
			$json = @file_get_contents($path);
			$rows = $json ? json_decode($json, true) : [];
			if (is_array($rows)) {
				foreach ($rows as $r) {
					if ((int)($r['id'] ?? 0) === (int)$id) { $record = $r; break; }
				}
			}
		}
		$all = $request->session()->get('ha_session_records', []);
		$hearingAids = [];
		if (is_array($all)) {
			$hearingAids = $all[(int)$id] ?? [];
			usort($hearingAids, function($a,$b){
				return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0');
			});
		}
		$svcAll = $request->session()->get('svc_session_records', []);
		$svcMap = [];
		if (is_array($svcAll)) {
			$svcMap = $svcAll[(int)$id] ?? [];
			foreach ($svcMap as $svc => $rows) {
				if (is_array($rows)) {
					usort($rows, function($a,$b){ return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0'); });
					$svcMap[$svc] = $rows;
				}
			}
		}
		if (empty($svcMap) && Schema::hasTable('tbl_test')) {
			$tests = Test::where('patient_id', (int)$id)->orderByDesc('test_date')->get();
			
			// If no tests for this file-backed id, try to match a DB patient by name + birthday
			if (($tests->count() ?? 0) === 0 && $record) {
				try {
					$first = trim(strtolower(($record['first_name'] ?? '')));
					$middle = trim(strtolower(($record['middle_name'] ?? '')));
					$last = trim(strtolower(($record['last_name'] ?? '')));
					$bday = trim((string)($record['birthday'] ?? ''));
					
					if ($first || $last) {
						$query = DB::table('tbl_patient')->select('patient_id','patient_firstname','patient_middlename','patient_surname','patient_birthdate');
						$query->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [$first]);
						$query->whereRaw('LOWER(TRIM(patient_surname)) = ?', [$last]);
						if ($bday) { $query->where('patient_birthdate', 'like', substr($bday,0,10).'%'); }
						$match = $query->first();
						if ($match && isset($match->patient_id)) {
							$matchedId = (int)$match->patient_id;
							$tests = Test::where('patient_id', $matchedId)->orderByDesc('test_date')->get();
							// Debug: Log the match
							\Log::info('Matched patient by name: ' . $first . ' ' . $last . ' -> DB patient_id: ' . $matchedId . ', found ' . $tests->count() . ' tests');
						}
					}
				} catch (\Throwable $e) {
					// ignore fallback failure; keep $tests as empty collection
					\Log::error('Error matching patient by name: ' . $e->getMessage());
				}
			}
			
			// Debug: Log what we're looking for and what we found
			\Log::info('Looking for tests with patient_id: ' . (int)$id);
			\Log::info('Found ' . $tests->count() . ' tests for this patient_id');
			
			$rebuilt = [];
			$reverseMap = [
				'Otoacoustic Emission (OAE)' => 'oae',
				'Pure Tone Audiometry' => 'pta',
				'Tympanometry' => 'tym',
				'Speech Audiometry' => 'speech',
				'Auditory Brainstem Response (ABR)' => 'abr',
				'Auditory Brain Response' => 'abr',
				'Auditory Steady State Response (ASSR)' => 'assr',
				'Aided Testing' => 'aided',
				'Play Audiometry' => 'play',
				'Hearing Aid Fitting' => 'hearing',
			];
			foreach ($tests as $t) {
				$payload = [];
				if (!empty($t->test_payload)) {
					$decoded = json_decode($t->test_payload, true);
					if (is_array($decoded)) { $payload = $decoded; }
				}
				$fakeId = spl_object_id($t);
				$svcKey = $reverseMap[$t->test_type] ?? null;
				if (!$svcKey) continue;
				$base = array_merge($payload, [
					'id' => $fakeId,
					'patient_id' => (int)$id,
					'service' => $svcKey,
					'date_taken' => $t->test_date,
					'created_at' => $t->test_date.' 00:00:00',
				]);
				$rebuilt[$svcKey][] = $base;
			}
			$svcMap = $rebuilt;
		}
		
		// Add hearing aid data from session (same as appointment record)
		$haAll = $request->session()->get('ha_session_records', []);
		if (is_array($haAll) && isset($haAll[(int)$id])) {
			$hearingAids = $haAll[(int)$id];
			if (is_array($hearingAids) && count($hearingAids) > 0) {
				$svcMap['hearing'] = $hearingAids;
			}
		}

		// Also get service records from session (same as appointment record)
		$svcAll = $request->session()->get('svc_session_records', []);
		if (is_array($svcAll)) {
			$sessionSvcMap = $svcAll[(int)$id] ?? [];
			foreach ($sessionSvcMap as $svc => $rows) {
				if (is_array($rows)) {
					usort($rows, function($a,$b){ 
						return strtotime($b['created_at'] ?? '0') <=> strtotime($a['created_at'] ?? '0'); 
					});
					// Merge session data with database data
					if (!isset($svcMap[$svc])) {
						$svcMap[$svc] = [];
					}
					$svcMap[$svc] = array_merge($svcMap[$svc], $rows);
				}
			}
		}
		
		$svcRecords = $svcMap;
		return view('admin.admin-patient-details', compact('id', 'record', 'hearingAids', 'svcRecords'));
	}

	/**
	 * API endpoint to get patient data with test results
	 */
	public function getPatientData(Request $request, $id)
	{
		if (!Auth::check() || Auth::user()->role !== 'admin') {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		$path = storage_path('app/patient_records.json');
		$record = null;
		if (file_exists($path)) {
			$json = @file_get_contents($path);
			$rows = $json ? json_decode($json, true) : [];
			if (is_array($rows)) {
				foreach ($rows as $r) {
					if ((int)($r['id'] ?? 0) === (int)$id) { $record = $r; break; }
				}
			}
		}

		return response()->json([
			'success' => true,
			'data' => $record
		]);
	}
}


