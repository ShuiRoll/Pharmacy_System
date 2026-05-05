@extends('layouts.app')

@section('page_title', 'Users')

@section('content')
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-bold text-white">Users</h1>
        <p class="text-white/80">Manage admin and staff accounts</p>
    </div>
    <a href="{{ route('users.create') }}"
       class="btn btn-primary flex items-center gap-2">
        <i class="fas fa-user-plus"></i> Add User
    </a>
</div>

<div class="rounded-[1.5rem] border border-white/10 bg-white/5 shadow-sm overflow-hidden">
    <table class="w-full">
        <thead>
            <tr class="border-b bg-slate-900/50">
                <th class="text-left p-6 font-medium text-white/80 text-xs uppercase tracking-[0.2em]">Name</th>
                <th class="text-left p-6 font-medium text-white/80 text-xs uppercase tracking-[0.2em]">Email</th>
                <th class="text-center p-6 font-medium text-white/80 text-xs uppercase tracking-[0.2em]">Role</th>
                <th class="text-left p-6 font-medium text-white/80 text-xs uppercase tracking-[0.2em]">Profile</th>
                <th class="w-56 p-6 text-right font-medium text-white/80 text-xs uppercase tracking-[0.2em]">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-white/10">
            @foreach($users as $user)
            <tr class="hover:bg-white/5">
                <td class="p-6 font-medium text-white">{{ $user->name }}</td>
                <td class="p-6 text-white/80">{{ $user->email }}</td>
                <td class="p-6 text-center">
                    <span class="status-pill {{ $user->role === 'admin' ? 'status-success' : 'status-warning' }}\">
                        {{ ucfirst($user->role) }}
                    </span>
                </td>
                <td class="p-6 text-sm text-white/80">
                    @if($user->role === 'admin')
                        Admin profile
                    @else
                        Staff profile
                    @endif
                </td>
                <td class="w-56 p-6 text-right align-middle whitespace-nowrap">
                    <div class="inline-flex items-center justify-end gap-3 whitespace-nowrap">
                        <a href="{{ route('users.edit', $user) }}" class="table-action">Edit</a>
                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-flex" onsubmit="return confirm('Delete this user?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="table-action action-danger">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@if($users instanceof \Illuminate\Pagination\LengthAwarePaginator || $users instanceof \Illuminate\Pagination\Paginator)
    <div class="mt-5 flex justify-end">
        {{ $users->links() }}
    </div>
@endif
@endsection
