<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
	public function preview(Request $req)
	{
		$data = $req->except(['_token']);
		session(['booking_preview' => $data]);
		if ($req->wantsJson() || $req->ajax() || $req->header('X-Requested-With') === 'XMLHttpRequest') {
			return response()->json(['status' => 'ok', 'message' => 'Preview saved']);
		}
		return view('patient.book-preview', compact('data'));
	}
}


