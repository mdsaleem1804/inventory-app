<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_STAFF])],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function index()
    {
        $users = User::query()->orderBy('name')->paginate(15);

        $roles = [
            User::ROLE_ADMIN,
            User::ROLE_MANAGER,
            User::ROLE_STAFF,
        ];

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function updateRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_STAFF])],
        ]);

        // Prevent accidental lockout by downgrading the final admin account.
        if ($user->isAdmin() && $validated['role'] !== User::ROLE_ADMIN) {
            $adminCount = User::query()->where('role', User::ROLE_ADMIN)->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'At least one Admin account is required.');
            }
        }

        $user->update(['role' => $validated['role']]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User role updated successfully.');
    }

    public function updateAccount(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in([User::ROLE_ADMIN, User::ROLE_MANAGER, User::ROLE_STAFF])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        // Prevent accidental lockout by downgrading the final admin account.
        if ($user->isAdmin() && $validated['role'] !== User::ROLE_ADMIN) {
            $adminCount = User::query()->where('role', User::ROLE_ADMIN)->count();
            if ($adminCount <= 1) {
                return redirect()->route('admin.users.index')
                    ->with('error', 'At least one Admin account is required.');
            }
        }

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', 'User account updated successfully.');
    }
}
