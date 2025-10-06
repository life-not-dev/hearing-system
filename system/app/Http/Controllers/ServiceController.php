<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::orderBy('id')->get();
        $isStaff = Auth::check() && Auth::user()->role === 'staff';
        if ($isStaff) {
            return view('staff.staff-services', compact('services', 'isStaff'));
        }
        return view('admin.admin-services', compact('services', 'isStaff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);
        Service::create($data);
        return back()->with('success','Service added');
    }

    public function update(Request $request, Service $service)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive'
        ]);
        $service->update($data);
        return back()->with('success','Service updated');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return back()->with('success','Service deleted');
    }
}
