<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('branch')->latest()->paginate(15);
        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('users.create', compact('branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,manager,staff'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'branch_id' => $validated['branch_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        AuditLog::log('USER_CREATED', "Created employee account {$user->name} ({$user->role})", $user);

        return redirect()->route('users.index')->with('success', "Staff account for {$user->name} created successfully.");
    }

    public function edit(User $user): View
    {
        $branches = Branch::where('is_active', true)->get();
        return view('users.edit', compact('user', 'branches'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:admin,manager,staff'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'branch_id' => $validated['branch_id'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role' => $validated['role'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $old = $user->toArray();
        $user->update($data);

        AuditLog::log('USER_UPDATED', "Updated employee account {$user->name}", $user, $old, $user->toArray());

        return redirect()->route('users.index')->with('success', "Account for {$user->name} updated successfully.");
    }

    public function toggleActive(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own administrative account.']);
        }

        $user->update(['is_active' => !$user->is_active]);

        $statusStr = $user->is_active ? 'activated' : 'deactivated';
        AuditLog::log('USER_STATUS_TOGGLED', "User {$user->name} was {$statusStr}", $user);

        return back()->with('success', "User {$user->name} has been {$statusStr}.");
    }
}
