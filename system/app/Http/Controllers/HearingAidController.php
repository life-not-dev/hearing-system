<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HearingAid;
use App\Repositories\HearingAidRepositoryInterface;

class HearingAidController extends Controller
{
    protected $hearingAids;

    public function __construct(HearingAidRepositoryInterface $hearingAids)
    {
        $this->hearingAids = $hearingAids;
    }

    public function index()
    {
        $hearingAids = $this->hearingAids->all();
        $isStaff = Auth::check() && Auth::user()->role === 'staff';

        return $isStaff
            ? view('staff.staff-hearing-aid', compact('hearingAids', 'isStaff'))
            : view('admin.admin-hearing-aid', compact('hearingAids', 'isStaff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|integer|min:0'
        ]);

        $this->hearingAids->create($data);

        return back()->with('success', 'Hearing aid added');
    }

    public function update(Request $request, HearingAid $hearingAid)
    {
        $data = $request->validate([
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'price' => 'required|integer|min:0'
        ]);

        $this->hearingAids->update($hearingAid, $data);

        return back()->with('success', 'Hearing aid updated');
    }

    public function destroy(HearingAid $hearingAid)
    {
        $this->hearingAids->delete($hearingAid);

        return back()->with('success', 'Hearing aid deleted');
    }
}
