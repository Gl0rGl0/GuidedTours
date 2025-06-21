@extends('layouts.app')

@section('title', 'Admin Configurator')

@section('content')

    <div class="container py-4">
        <h2 class="mb-4">Admin Configurator Panel</h2>

        <div class="row mb-5" id="places-visits">
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Registered Places</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if ($places->isNotEmpty())
                            <ul class="list-group flex-grow-1 overflow-auto mb-3">
                                @foreach ($places as $place)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $place->name }}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.places.edit', $place) }}" class="btn btn-secondary btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.places.destroy', $place) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted flex-grow-1">No places found.</p>
                        @endif
                        <a href="{{ route('admin.places.create') }}" class="btn btn-sm btn-primary align-self-start">+ Add Place</a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h4 class="mb-0">Registered Visit Types</h4>
                    </div>
                    <div class="card-body d-flex flex-column">
                        @if ($visit_types->isNotEmpty())
                            <ul class="list-group flex-grow-1 overflow-auto mb-3">
                                @foreach ($visit_types as $visit_type)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        {{ $visit_type->title }}
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('admin.visit-types.edit', $visit_type) }}" class="btn btn-secondary btn-sm">
                                                <i class="bi bi-pencil"></i> Edit
                                            </a>
                                            <form action="{{ route('admin.visit-types.destroy', $place) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="bi bi-trash"></i> Remove
                                                </button>
                                            </form>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted flex-grow-1">No visit types found.</p>
                        @endif
                        <a href="{{ route('admin.visit-types.create') }}" class="btn btn-sm btn-primary align-self-start">+ Add Visit Type</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="people-management">
            <div class="col-12 mb-3">
                <h3>People Management</h3>
            </div>

            @foreach ($users_by_role as $role => $users)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">{{ ucfirst($role) }}s</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            @if ($users->isNotEmpty())
                                <ul class="list-group flex-grow-1 overflow-auto mb-3">
                                    @foreach ($users as $user)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            {{ $user->username }}
                                            <div class="btn-group btn-group-sm">
                                                @if ($role !== 'configurator' && $user->user_id !== Auth::id())
                                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i> Remove
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted flex-grow-1">No users in this role.</p>
                            @endif
                            @if ($role !== 'fruitore')
                                <button class="btn btn-primary btn-sm align-self-start" data-bs-toggle="modal" data-bs-target="#addUserModal">
                                    + Add {{ ucfirst($role) }}
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="modal fade" id="addUserModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.users.add') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control text-center" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control text-center" id="password" name="password" minlength="6" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control text-center" id="password_confirmation" name="password_confirmation" required>
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Role</nlabel>
                            <select id="role" name="role" class="form-select text-center" required>
                                <option value="" disabled selected>-- Select Role --</option>
                                <option value="volunteer">Volunteer</option>
                                <option value="configurator">Configurator</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Add User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection