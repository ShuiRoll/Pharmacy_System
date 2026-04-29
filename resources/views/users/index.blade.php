@extends('layouts.app')

@section('page_title', 'Users')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold">Users</h1>
        <p class="text-gray-600 dark:text-gray-400">Manage admin and staff accounts</p>
    </div>
    <a href="{{ route('users.create') }}"
       class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl font-medium flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Add User
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-3xl shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-gray-50 dark:bg-gray-700">
                <th class="text-left p-6 font-medium">Name</th>
                <th class="text-left p-6 font-medium">Email</th>
                <th class="text-center p-6 font-medium">Role</th>
                <th class="text-left p-6 font-medium">Profile</th>
                <th class="w-40 p-6 text-right font-medium">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @foreach($users as $user)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="p-6 font-medium">{{ $user->name }}</td>
                <td class="p-6 text-gray-600">{{ $user->email }}</td>
                <td class="p-6 text-center">
                    <span class="px-4 py-1 rounded-full text-xs font-medium {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="p-6 text-sm text-gray-500">
                    @if($user->role === 'admin')
                        Admin profile
                    @else
                        Staff profile
                    @endif
                </td>
                <td class="p-6 text-right">
                    <div class="flex items-center justify-end gap-3">
                        <a href="{{ route('users.edit', $user) }}" class="text-blue-600 hover:underline">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:underline">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
