<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Branch;

class UserController extends Controller
{
    /**
     * Show list of users (admin only)
     */
    public function index($type = 'all')
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        // Normalize type value
        $type = strtolower($type);
        if (!in_array($type, ['all','admin','staff','patient'])) {
            $type = 'all';
        }

        // Base queries with branch relationship
        $staffQuery = User::with('branchRef')->whereIn('role', ['admin','staff'])->orderBy('id','asc');
        $patientQuery = User::where('role','patient')->orderBy('id','asc');

        // Apply type-specific filtering so only requested dataset(s) are paginated/fetched
        $staffAdmins = null;
        $patients = null;
        if (in_array($type, ['all','admin','staff'])) {
            if ($type === 'admin') {
                $staffQuery->where('role','admin');
            } elseif ($type === 'staff') {
                $staffQuery->where('role','staff');
            }
            $staffAdmins = $staffQuery->paginate(5, ['*'], 'staff_page');
        }
        if (in_array($type, ['all','patient'])) {
            $patients = $patientQuery->paginate(5, ['*'], 'patient_page');
        }

        $branches = [];
        try {
            if (DB::getSchemaBuilder()->hasTable('tbl_branch')) {
                $branches = Branch::orderBy('branch_name')->get(['branch_id','branch_name']);
            }
        } catch (\Throwable $e) { $branches = []; }
        return view('admin.user-account.list', compact('staffAdmins','patients','type','branches'));
    }

    /**
     * Show registration form for specified role
     */
    public function create($role = 'admin')
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        
        return view('admin.user-account.register', compact('role'));
    }

    /**
     * Store new user with specified role
     */
    public function store(Request $request, $role = null)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        
        // Use role from route parameter if provided, otherwise from form
        $targetRole = $role ?? $request->input('role', 'admin');
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role' => 'sometimes|in:admin,staff',
            'branch' => 'nullable|string|max:255',
        ]);
        
        // Override role with parameter if provided
        $data['role'] = $targetRole;
        
        // Map branch codes to friendly names if codes used
        $branchMap = [
            'cdo' => 'Cagayan De Oro City Branch',
            'davao' => 'Davao City Branch',
            'butuan' => 'Butuan City Branch',
        ];
        if (isset($branchMap[$data['branch'] ?? ''])) {
            $data['branch'] = $branchMap[$data['branch']];
        }

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
            'branch' => $data['branch'] ?? null,
        ]);

        return redirect()->route('admin.user.account.list')->with('success', ucfirst($data['role']) . ' account created successfully.');
    }

    /**
     * Show user details
     */
    public function show($id, $role = null)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        
        $user = User::findOrFail($id);
        return view('admin.user-account.show', compact('user', 'role'));
    }

    /**
     * Update user account
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }

        $user = User::findOrFail($id);
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email:rfc,dns|unique:users,email,' . $id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        // Update user data
        $user->name = $data['name'];
        $user->email = $data['email'];
        
        // Only update password if provided
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        
        $user->save();

        return redirect()->route('admin.user.account.list')
            ->with('success', ucfirst($user->role) . " account '{$user->name}' updated successfully.");
    }

    /**
     * Delete user account
     */
    public function destroy($id, $role = null)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        // Prevent self-deletion to avoid locking out current admin
        if ((int)$id === (int)Auth::id()) {
            return redirect()->route('admin.user.account.list')->with('error', 'You cannot delete your own account while logged in.');
        }

        $user = User::findOrFail($id);
        $userRole = $user->role;
        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.user.account.list')
            ->with('success', ucfirst($userRole) . " account ('{$userName}') deleted successfully.");
    }

    /**
     * Store a new patient account (staff action)
     */
    public function patientStore(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'staff') {
            return redirect()->route('login');
        }

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:users,name',
            'email' => 'required|email:rfc,dns|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $patient = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'patient',
        ]);

        return redirect()->route('staff.patient.register')->with('success', "Patient account '{$patient->name}' created.");
    }

    /**
     * Assign a branch to a user (admin only). Accepts branch_id from tbl_branch.
     */
    public function setBranch(Request $request, $id)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            return redirect()->route('login');
        }
        $data = $request->validate([
            'branch_id' => 'required|integer',
        ]);
        $user = User::findOrFail($id);
        if (!in_array($user->role, ['admin','staff'])) {
            return back()->with('error', 'Only admin/staff can be assigned to a branch.');
        }
        // Ensure branch exists
        $branch = Branch::find($data['branch_id']);
        if (!$branch) {
            return back()->with('error', 'Selected branch not found.');
        }
        // Update both branch_id and legacy branch name for compatibility
        $user->branch_id = $branch->branch_id;
        $user->branch = $branch->branch_name;
        $user->save();
        return back()->with('success', "Assigned '{$user->name}' to branch '{$branch->branch_name}'.");
    }
}