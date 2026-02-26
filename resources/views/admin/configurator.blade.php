@extends('layouts.app')

@section('title', 'Admin Configurator')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5">
        <div class="mb-3 mb-md-0">
            <h2 class="fw-bold text-primary mb-1">Configuration Panel</h2>
            <p class="text-muted mb-0">System management and content administration</p>
        </div>
        <div class="d-flex gap-2">
             <!-- Quick Actions Dropdown could go here if needed -->
        </div>
    </div>

    <!-- Navigation Tabs (Heuristic 8: Aesthetic and minimalist design) -->
    <!-- Reduces clutter by organizing content into views -->
    
    <!-- Analytics Section -->
    <script>
        window.userGrowthStats = @json($monthlyStats);
    </script>
    <div class="row mb-5" 
         x-data="{
            init() {
                var ctx = this.$refs.canvas.getContext('2d');
                var gradient = ctx.createLinearGradient(0, 0, 0, 400);
                gradient.addColorStop(0, 'rgba(79, 70, 229, 0.2)');
                gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

                const stats = window.userGrowthStats || [];
                const labels = stats.map(s => s.month);
                const counts = stats.map(s => s.count);

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'New Users',
                            data: counts,
                            borderColor: '#4f46e5',
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                titleColor: '#333',
                                bodyColor: '#666',
                                borderColor: '#ddd',
                                borderWidth: 1
                            }
                        },
                        scales: {
                            y: { 
                                beginAtZero: true, 
                                grid: { borderDash: [2, 4] },
                                ticks: { precision: 0 } 
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }
         }">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                     <div>
                         <h5 class="fw-bold mb-0">Customer Growth</h5>
                         <p class="text-muted small mb-0">New customer registrations (Last 6 Months)</p>
                     </div>
                </div>
                <div class="card-body position-relative px-4 pb-4" style="height: 320px;">
                    <canvas x-ref="canvas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-white rounded-5 shadow-sm mb-4" id="configTabs" role="tablist" style="max-width: 600px; margin: 0 auto;">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-5 fw-bold" id="places-tab" data-bs-toggle="tab" data-bs-target="#places" type="button" role="tab" aria-selected="true">
                <i class="bi bi-geo-alt me-2"></i>Places
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-5 fw-bold" id="types-tab" data-bs-toggle="tab" data-bs-target="#types" type="button" role="tab" aria-selected="false">
                <i class="bi bi-tags me-2"></i>Visit Types
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-5 fw-bold" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-selected="false">
                <i class="bi bi-people me-2"></i>Users
            </button>
        </li>
    </ul>

    <div class="tab-content" id="configTabsContent">
        
        <!-- PLACES TAB -->
        <div class="tab-pane fade show active" id="places" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">Manage Places</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="searchPlaces" placeholder="Search places...">
                        </div>
                        <a href="{{ route('admin.places.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center">
                            <i class="bi bi-plus-lg me-1"></i> Add Place
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="placesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small text-uppercase font-weight-bold border-0">Name</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Location</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Description</th>
                                    <th class="text-end pe-4 text-muted small text-uppercase font-weight-bold border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($places as $place)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $place->name }}</td>
                                        <td class="text-muted"><i class="bi bi-pin-map me-1"></i>{{ Str::limit($place->location, 30) }}</td>
                                        <td class="text-muted small">{{ Str::limit($place->description, 50) }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.places.edit', $place) }}" class="btn btn-icon btn-light btn-sm rounded-circle text-muted" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon btn-light btn-sm rounded-circle text-danger ms-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-action="{{ route('admin.places.destroy', $place) }}"
                                                data-item-name="{{ $place->name }}"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="p-0 border-0">
                                            <x-empty-state 
                                                icon="bi-geo-alt" 
                                                title="No places found" 
                                                message="Start by adding one." 
                                                :card="false" 
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- VISIT TYPES TAB -->
        <div class="tab-pane fade" id="types" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">Manage Visit Types</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="searchTypes" placeholder="Search types...">
                        </div>
                        <a href="{{ route('admin.visit-types.create') }}" class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center">
                            <i class="bi bi-plus-lg me-1"></i> Add Visit Type
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="typesTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small text-uppercase font-weight-bold border-0">Title</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Place</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Details</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Capacity</th>
                                     <th class="text-end pe-4 text-muted small text-uppercase font-weight-bold border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($visit_types as $type)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">{{ $type->title }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $type->place?->name ?? 'Unassigned' }}</span></td>
                                        <td class="small text-muted">
                                            <div><i class="bi bi-clock me-1"></i>{{ $type->duration_minutes }} min</div>
                                            <div><i class="bi bi-geo me-1"></i>{{ Str::limit($type->meeting_point, 20) }}</div>
                                        </td>
                                        <td class="small text-muted">
                                            <i class="bi bi-people me-1"></i> {{ $type->min_participants }}-{{ $type->max_participants }}
                                        </td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('admin.visit-types.edit', $type) }}" class="btn btn-icon btn-light btn-sm rounded-circle text-muted" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-icon btn-light btn-sm rounded-circle text-danger ms-1" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#deleteModal"
                                                data-action="{{ route('admin.visit-types.destroy', $type) }}"
                                                data-item-name="{{ $type->title }}"
                                                title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-0 border-0">
                                            <x-empty-state 
                                                icon="bi-tags" 
                                                title="No visit types defined yet." 
                                                message="" 
                                                :card="false" 
                                            />
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- USERS TAB -->
        <div class="tab-pane fade" id="users" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h5 class="fw-bold mb-0">User Management</h5>
                    <div class="d-flex gap-2">
                        <div class="input-group input-group-sm" style="width: 200px;">
                            <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                            <input type="text" class="form-control bg-light border-0" id="searchUsers" placeholder="Search users...">
                        </div>
                        <button class="btn btn-primary btn-sm rounded-pill px-3 d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="bi bi-person-plus me-1"></i> Add User
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                         <table class="table table-hover align-middle mb-0" id="usersTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 text-muted small text-uppercase font-weight-bold border-0">User</th>
                                    <th class="text-muted small text-uppercase font-weight-bold border-0">Role</th>
                                    <th class="text-end pe-4 text-muted small text-uppercase font-weight-bold border-0">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users_by_role as $role => $users)
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar bg-primary-subtle text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                                        {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                        <div class="small text-muted">{{ $user->email }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @php
                                                    $badgeColor = match($role) {
                                                        'Admin' => 'bg-danger-subtle text-danger',
                                                        'Guide' => 'bg-info-subtle text-info-emphasis',
                                                        default => 'bg-light text-dark',
                                                    };
                                                @endphp
                                                <span class="badge {{ $badgeColor }} rounded-pill px-3 py-2 border-0 fw-normal text-capitalize">
                                                    {{ $role }}
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if($role !== 'Admin' && $user->id !== Auth::id())
                                                     <button type="button" class="btn btn-icon btn-light btn-sm rounded-circle text-danger" 
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#deleteModal"
                                                        data-action="{{ route('admin.users.destroy', $user) }}"
                                                        data-item-name="{{ $user->first_name }} {{ $user->last_name }}"
                                                        title="Delete User">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                @else
                                                    @php
                                                        $tooltipMessage = 'Administrators cannot be deleted.';
                                                    @endphp
                                                    <span class="d-inline-block" tabindex="0" data-bs-toggle="tooltip" title="{{ $tooltipMessage }}">
                                                        <button class="btn btn-icon btn-light btn-sm rounded-circle" type="button" disabled>
                                                            <i class="bi bi-trash text-muted opacity-50"></i>
                                                        </button>
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                         </table>
                    </div>
                     @if(collect($users_by_role)->flatten()->isEmpty())
                        <x-empty-state 
                            icon="bi-people" 
                            title="No users found." 
                            message="" 
                            :card="false" 
                        />
                    @endif
                </div>
            </div>
        </div>

    </div>
<!-- ADD USER MODAL -->
<div class="modal fade" id="addUserModal" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
             <form action="{{ route('admin.users.add') }}" method="POST">
                @csrf
                <div class="modal-body pt-4">
                    <div class="row g-2 mb-3">
                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="new_first_name" name="first_name" placeholder="First Name" required>
                            <label for="new_first_name">First Name</label>
                        </div>
                        <div class="col-md-6 form-floating">
                            <input type="text" class="form-control" id="new_last_name" name="last_name" placeholder="Last Name" required>
                            <label for="new_last_name">Last Name</label>
                        </div>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="new_email" name="email" placeholder="Email Address" required>
                        <label for="new_email">Email</label>
                    </div>
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="new_password" name="password" placeholder="Password" minlength="6" required>
                        <label for="new_password">Password</label>
                        <div class="form-text text-muted small" id="adminPasswordHelp"><i class="bi bi-info-circle me-1"></i>Min. 6 characters</div>
                    </div>
                    <div class="form-floating mb-3">
                         <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" placeholder="Confirm" minlength="6" required>
                        <label for="new_password_confirmation">Confirm Password</label>
                        <div class="form-text text-muted small d-none" id="adminConfirmHelp"><i class="bi bi-check-circle me-1"></i>Passwords match</div>
                    </div>
                    <div class="form-floating">
                        <select id="new_role" name="role" class="form-select" required>
                            <option value="" disabled selected>Select Role</option>
                            <option value="Guide">Guide</option>
                            <option value="Admin">Admin</option>
                        </select>
                        <label for="new_role">Role</label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Confirm Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-0">Are you sure you want to remove <strong id="deleteItemName" class="text-dark">item</strong>? This action cannot be undone.</p>
            </div>
             <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger rounded-pill px-4">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. Client-Side Search (Heuristic 7: Flexibility and efficiency of use) ---
        function setupSearch(inputId, tableId) {
            const input = document.getElementById(inputId);
            const table = document.getElementById(tableId);
            if (!input || !table) return;

            input.addEventListener('keyup', function() {
                const filter = input.value.toLowerCase();
                const rows = table.getElementsByTagName('tr');

                // Start from 1 to skip header
                for (let i = 1; i < rows.length; i++) {
                    const row = rows[i];
                    const text = row.textContent || row.innerText;
                    if (text.toLowerCase().indexOf(filter) > -1) {
                        row.style.display = "";
                    } else {
                        row.style.display = "none";
                    }
                }
            });
        }

        setupSearch('searchPlaces', 'placesTable');
        setupSearch('searchTypes', 'typesTable');
        setupSearch('searchUsers', 'usersTable');


        // --- 2. Modal Handling ---
        var deleteModal = document.getElementById('deleteModal');
        deleteModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var actionUrl = button.getAttribute('data-action');
            var itemName = button.getAttribute('data-item-name');
            
            var form = document.getElementById('deleteForm');
            form.action = actionUrl;
            
            document.getElementById('deleteItemName').textContent = itemName;
        });
        
        // Auto-focus input on modal show (Heuristic 7)
        var addUserModal = document.getElementById('addUserModal');
         addUserModal.addEventListener('shown.bs.modal', function () {
            document.getElementById('new_first_name').focus();
        });

        // Live Password Validation (Issue 28)
        const newPassword = document.getElementById('new_password');
        const newConfirm = document.getElementById('new_password_confirmation');
        const adminPassHelp = document.getElementById('adminPasswordHelp');
        const adminConfHelp = document.getElementById('adminConfirmHelp');
        const addUserBtn = addUserModal.querySelector('button[type="submit"]');

        function validateAdminPasswords() {
            const pVal = newPassword.value;
            const cVal = newConfirm.value;
            let pValid = false;
            let cValid = false;

            if (pVal.length >= 6) {
                adminPassHelp.className = 'form-text text-success small';
                adminPassHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Min. 6 characters';
                pValid = true;
            } else if (pVal.length > 0) {
                adminPassHelp.className = 'form-text text-danger small';
                adminPassHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Min. 6 characters';
            } else {
                adminPassHelp.className = 'form-text text-muted small';
                adminPassHelp.innerHTML = '<i class="bi bi-info-circle me-1"></i>Min. 6 characters';
            }

            if (cVal.length > 0) {
                adminConfHelp.classList.remove('d-none');
                if (pVal === cVal) {
                    adminConfHelp.className = 'form-text text-success small';
                    adminConfHelp.innerHTML = '<i class="bi bi-check-circle me-1"></i>Passwords match';
                    cValid = true;
                } else {
                    adminConfHelp.className = 'form-text text-danger small';
                    adminConfHelp.innerHTML = '<i class="bi bi-x-circle me-1"></i>Passwords do not match';
                }
            } else {
                adminConfHelp.classList.add('d-none');
            }

            addUserBtn.disabled = !(pValid && cValid);
        }

        newPassword.addEventListener('input', validateAdminPasswords);
        newConfirm.addEventListener('input', validateAdminPasswords);
        
        // Initial state disable
        addUserBtn.disabled = true;

        // --- 3. Initialize tooltips ---
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    });
</script>
@endpush
@endsection