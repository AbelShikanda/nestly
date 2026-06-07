<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    /**
     * List all users
     */
    public function index(Request $request)
    {
        $users = User::query();
        
        if ($request->filled('search')) {
            $users->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
        }
        
        if ($request->filled('role')) {
            $users->where('role', $request->role);
        }
        
        $users = $users->latest()->paginate(20);
        
        return view('admin.users.index', compact('users'));
    }

    /**
     * Show single user
     */
    public function show($id)
    {
        $user = User::with('properties', 'subscriptions')->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Create new user (admin)
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store new user
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:tenant,landlord,admin',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User created successfully!');
    }

    /**
     * Edit user
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update user
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'role' => 'required|in:tenant,landlord,admin',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.show', $user)
            ->with('success', 'User updated successfully!');
    }

    /**
     * Delete user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        // Don't allow deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        $user->delete();
        
        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully!');
    }

    /**
     * Toggle user role
     */
    public function toggleRole($id)
    {
        $user = User::findOrFail($id);
        
        $roles = ['tenant', 'landlord', 'admin'];
        $currentIndex = array_search($user->role, $roles);
        $nextRole = $roles[($currentIndex + 1) % count($roles)];
        
        $user->update(['role' => $nextRole]);
        
        return back()->with('success', "User role changed to: " . ucfirst($nextRole));
    }

    /**
     * Verify user (landlord verification)
     */
    public function verify($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_verified' => !$user->is_verified]);
        
        $status = $user->is_verified ? 'verified' : 'unverified';
        return back()->with('success', "User has been {$status}!");
    }

    /**
     * Suspend user
     */
    public function suspend($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot suspend yourself.');
        }
        
        $user->update(['is_suspended' => !($user->is_suspended ?? false)]);
        
        $status = $user->is_suspended ? 'suspended' : 'activated';
        return back()->with('success', "User has been {$status}!");
    }
}