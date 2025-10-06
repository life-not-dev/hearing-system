<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Test;

class StaffPatientRecordController extends Controller
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
		
		// Add hearing aid data from session (prefer records keyed by the file-backed id)
		$haAll = $request->session()->get('ha_session_records', []);
		if (is_array($haAll)) {
			if (isset($haAll[(int)$id]) && is_array($haAll[(int)$id]) && count($haAll[(int)$id]) > 0) {
				$hearingAids = $haAll[(int)$id];
				$svcMap['hearing'] = $hearingAids;
			} else {
				// fallback: try matching DB patient by name + birthday and look for session entries under that id
				if ($record) {
					try {
						$first = trim(strtolower(($record['first_name'] ?? '')));
						$last = trim(strtolower(($record['last_name'] ?? '')));
						$bday = trim((string)($record['birthday'] ?? ''));
						if ($first || $last) {
							$query = DB::table('tbl_patient')->select('patient_id');
							$query->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [$first]);
							$query->whereRaw('LOWER(TRIM(patient_surname)) = ?', [$last]);
							if ($bday) { $query->where('patient_birthdate', 'like', substr($bday,0,10).'%'); }
							$match = $query->first();
							if ($match && isset($match->patient_id)) {
								$matchedId = (int)$match->patient_id;
								if (isset($haAll[$matchedId]) && is_array($haAll[$matchedId]) && count($haAll[$matchedId]) > 0) {
									$hearingAids = $haAll[$matchedId];
									$svcMap['hearing'] = $hearingAids;
									\Log::info('StaffPatientRecordController::details - used session hearing records from DB patient id ' . $matchedId . ' for file id ' . $id);
								}
							}
						}
					} catch (\Throwable $e) {
						\Log::error('Error matching DB patient for hearing session fallback (details): ' . $e->getMessage());
					}
				}
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
		return view('services.staff-patient-details', compact('id', 'record', 'hearingAids', 'svcRecords'));
	}

	/**
	 * API endpoint to get patient data with test results
	 */
	public function getPatientData(Request $request, $id)
	{
		if (!Auth::check() || Auth::user()->role !== 'staff') {
			return response()->json(['error' => 'Unauthorized'], 401);
		}

		try {
			// Get patient record from JSON file
			$path = storage_path('app/patient_records.json');
			$record = null;
			if (file_exists($path)) {
				$json = @file_get_contents($path);
				$rows = $json ? json_decode($json, true) : [];
				if (is_array($rows)) {
					foreach ($rows as $r) {
						if ((int)($r['id'] ?? 0) === (int)$id) { 
							$record = $r; 
							break; 
						}
					}
				}
			}

			if (!$record) {
				return response()->json(['error' => 'Patient record not found'], 404);
			}

			// Use the SAME logic as appointment record to get test results
			$svcMap = [];
			
			// First, try to get from database (same as appointment record)
			if (Schema::hasTable('tbl_test')) {
				$tests = Test::where('patient_id', (int)$id)->orderByDesc('test_date')->get();

				// If no tests for this file-backed id, try to match a DB patient by name + birthday
				if (($tests->count() ?? 0) === 0) {
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
								// tag records with real patient id
								$foundBy = $matchedId;
							}
						}
					} catch (\Throwable $e) {
						// ignore fallback failure; keep $tests as empty collection
					}
				}
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
						$dec = json_decode($t->test_payload, true); 
						if (is_array($dec)) $payload = $dec; 
					}
					$svcKey = $reverseMap[$t->test_type] ?? null;
					if (!$svcKey) continue;
					$base = array_merge($payload, [
						'id' => spl_object_id($t),
						'patient_id' => (int)$id,
						'service' => $svcKey,
						'date_taken' => $t->test_date,
						'created_at' => ($t->test_date ? ($t->test_date.' 00:00:00') : null),
					]);
					$svcMap[$svcKey][] = $base;
				}
			}

			// Add hearing aid data from session (same as appointment record)
			$haAll = $request->session()->get('ha_session_records', []);
			if (is_array($haAll)) {
				if (isset($haAll[(int)$id]) && is_array($haAll[(int)$id]) && count($haAll[(int)$id]) > 0) {
					$hearingAids = $haAll[(int)$id];
					$svcMap['hearing'] = $hearingAids;
				} else {
					// fallback: try matching DB patient by name + birthday and look for session entries under that id
					if ($record) {
						try {
							$first = trim(strtolower(($record['first_name'] ?? '')));
							$last = trim(strtolower(($record['last_name'] ?? '')));
							$bday = trim((string)($record['birthday'] ?? ''));
							if ($first || $last) {
								$query = DB::table('tbl_patient')->select('patient_id');
								$query->whereRaw('LOWER(TRIM(patient_firstname)) = ?', [$first]);
								$query->whereRaw('LOWER(TRIM(patient_surname)) = ?', [$last]);
								if ($bday) { $query->where('patient_birthdate', 'like', substr($bday,0,10).'%'); }
								$match = $query->first();
								if ($match && isset($match->patient_id)) {
									$matchedId = (int)$match->patient_id;
									if (isset($haAll[$matchedId]) && is_array($haAll[$matchedId]) && count($haAll[$matchedId]) > 0) {
										$hearingAids = $haAll[$matchedId];
										$svcMap['hearing'] = $hearingAids;
										\Log::info('StaffPatientRecordController::getPatientData - used session hearing records from DB patient id ' . $matchedId . ' for file id ' . $id);
									}
								}
							}
						} catch (\Throwable $e) {
							\Log::error('Error matching DB patient for hearing session fallback (API): ' . $e->getMessage());
						}
					}
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

			// Debug: Log the data being returned
			\Log::info('Patient data API response for ID ' . $id, [
				'record' => $record,
				'service_records' => $svcMap,
				'database_tests_count' => Test::where('patient_id', (int)$id)->count(),
				'session_data_keys' => array_keys($request->session()->all())
			]);

			return response()->json([
				'patient_record' => $record,
				'service_records' => $svcMap,
				'success' => true
			]);

		} catch (\Throwable $e) {
			\Log::error('Error in getPatientData: ' . $e->getMessage(), [
				'patient_id' => $id,
				'trace' => $e->getTraceAsString()
			]);
			return response()->json(['error' => 'Failed to fetch patient data: ' . $e->getMessage()], 500);
		}
	}

	protected function loadRecord($id)
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
		return $record;
	}

	public function oae($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-oae', compact('id', 'record'));
	}

	public function abr($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-abr', compact('id', 'record'));
	}

	public function assr($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-assr', compact('id', 'record'));
	}

	public function pta($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-pta', compact('id', 'record'));
	}

	public function tym($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-tym', compact('id', 'record'));
	}

	public function speech($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-speech', compact('id', 'record'));
	}

	public function hearing($id)
	{
		$record = $this->loadRecord($id);
		return view('services.staff-patient-hearing', compact('id', 'record'));
	}
}


