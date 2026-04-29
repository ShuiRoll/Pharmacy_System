<?php

namespace App\Http\Controllers;

use App\Models\CycleCount;
use App\Models\InboundTransaction;
use App\Models\InventoryAdjustment;
use App\Models\OutboundTransaction;
use App\Models\Sale;
use App\Models\SaleReturn;
use App\Models\SystemAlert;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $emails = User::pluck('email')->values();

        return view('users.create', compact('emails'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);

        DB::transaction(function () use ($validated): void {
            User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => $validated['role'],
            ]);
        });

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $emails = User::whereKeyNot($user->getKey())->pluck('email')->values();

        return view('users.edit', compact('user', 'emails'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->getKey(), $user->getKeyName())],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => ['required', Rule::in(['admin', 'staff'])],
        ]);

        DB::transaction(function () use ($validated, $user): void {
            $user->name = $validated['name'];
            $user->email = $validated['email'];
            $user->role = $validated['role'];

            if (! empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();
        });

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->getKey()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
