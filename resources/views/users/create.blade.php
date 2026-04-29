@extends('layouts.app')

@section('page_title', 'Add User')

@section('content')
<div class="mx-auto max-w-3xl space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-white">Add User</h1>
        <p class="mt-1 text-sm text-slate-300">Create an admin or staff account.</p>
    </div>

    <div class="app-panel p-6 sm:p-8">
        <form action="{{ route('users.store') }}" method="POST" data-unique-form>
            @csrf

            <div class="grid gap-5 md:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Full Name <span class="text-rose-300">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input">
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Email <span class="text-rose-300">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                           data-unique-field="email"
                           data-unique-values='@json($emails ?? [])'
                           class="form-input">
                    <p data-unique-warning="email" class="mt-2 hidden text-sm text-amber-200">This email is already assigned to another user.</p>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Role <span class="text-rose-300">*</span></label>
                    <select name="role" required class="form-input">
                        <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                        <option value="staff" @selected(old('role', 'staff') === 'staff')>Staff</option>
                    </select>
                </div>

                <div></div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Password <span class="text-rose-300">*</span></label>
                    <div class="relative">
                        <input id="password" type="password" name="password" required class="form-input pr-12">
                        <button type="button" data-password-toggle="#password" aria-label="Show password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-medium text-slate-200">Confirm Password <span class="text-rose-300">*</span></label>
                    <div class="relative">
                        <input id="password-confirmation" type="password" name="password_confirmation" required class="form-input pr-12">
                        <button type="button" data-password-toggle="#password-confirmation" aria-label="Show password" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button type="submit" data-unique-submit class="btn btn-primary flex-1">Save User</button>
                <a href="{{ route('users.index') }}" class="btn btn-secondary flex-1">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
