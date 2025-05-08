@extends('layouts.app')

@section('title', 'Admin Configurator')

@push('styles')
{{-- Basic Admin Panel Styling - Consider moving to a dedicated CSS file --}}
<style>
    .admin-message { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
    .admin-message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .admin-message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* Styling for list containers (Places/Visits and User Roles) */
    .admin-list-box { /* Common style for all list boxes */
        flex: 1; /* Allow boxes to share space equally */
        min-width: 200px; /* Minimum width before wrapping */
        background-color: #f9f9f9; /* Light gray background */
        padding: 5px 15px 15px 15px; /* Reduced top padding (top, right, bottom, left) */
        border: 1px solid #eee;
        border-radius: 4px;
        margin-bottom: 15px; /* Keep bottom margin for wrapped items */
        display: flex; /* Use flexbox for internal layout */
        flex-direction: column; /* Stack content vertically */
    }

    #places-visits .admin-lists-container {
        display: flex;
        gap: 20px; /* Consistent gap */
        flex-wrap: wrap; /* Allow wrapping */
    }
    #places-visits .admin-list {
         /* Inherit common styles */
         /* Add specific styles if needed */
    }
    /* Remove top margin from headings inside the list boxes */
    .admin-list-box h4,
    .admin-list-box h5 {
        margin-top: 5px; /* Keep some top margin */
        margin-bottom: 10px; /* Increase bottom margin */
    }
    #places-visits ul.item-list, #manage-users ul { /* Target specific ul */
        list-style: none; /* Remove default bullets */
        margin: 0 0 15px 0; /* Add bottom margin before Add button */
        padding: 0;
        flex-grow: 1; /* Allow list to take up space */
        max-height: 300px; /* Set a max height for the list */
        overflow-y: auto; /* Add vertical scrollbar only when needed */
    }
    /* Set font size for list items within the boxes */
    .admin-list-box li {
        font-size: 16px;
        margin-bottom: 8px; /* Increase spacing between items */
        border-bottom: 1px solid #eee; /* Separator line */
        padding-bottom: 8px; /* Space below separator */
    }
     .admin-list-box li:last-child {
        border-bottom: none; /* Remove border from last item */
        margin-bottom: 0;
        padding-bottom: 0; /* Remove padding for last item */
     }

    /* Styling for list items with actions (Places, Visits, Users) */
    .list-item-with-actions,
    .user-list-item { /* Apply to both types of list items */
        display: flex;
        flex-direction: row; /* Align items horizontally */
        justify-content: space-between; /* Push name and buttons apart */
        align-items: center; /* Vertically center items on the row */
        min-height: 30px; /* Adjust min-height if needed */
    }
    .list-item-with-actions .item-name,
    .user-list-item .username { /* Include username span here */
        margin-bottom: 0; /* Remove bottom margin */
        flex-grow: 1; /* Allow name to take available space */
        display: inline; /* Can be inline now */
        margin-right: 10px; /* Add space between name and buttons */
    }
    .action-buttons {
        display: flex;
        gap: 8px; /* Space between buttons */
        width: auto; /* Don't take full width */
        justify-content: flex-end; /* Keep alignment */
        margin-top: 0; /* Remove auto margin */
        padding-top: 0; /* Remove padding */
        flex-shrink: 0; /* Prevent buttons from shrinking */
    }
    /* Remove custom button styles - rely on Bootstrap classes */
    /* .action-button { ... } */
    /* .edit-button { ... } */
    /* .edit-button:hover { ... } */
    /* .remove-button, .remove-user-button { ... } */
    /* .remove-button:hover, .remove-user-button:hover { ... } */

    /* Styling for the Add User Modal Form */
    #modal-add-user-form {
        /* Apply common box style if desired, or define specific styles */
    }
    #modal-add-user-form div { margin-bottom: 10px; }
    #modal-add-user-form label { display: block; margin-bottom: 3px; font-weight: bold; }
    #modal-add-user-form input[type="text"],
    #modal-add-user-form input[type="text"],
    #modal-add-user-form input[type="password"],
    #modal-add-user-form select {
        width: 100%;
        padding: 8px;
        box-sizing: border-box;
        border: 1px solid #ccc; /* Add a standard border */
        border-radius: 4px; /* Slightly rounded corners */
        margin-bottom: 10px; /* Ensure spacing below inputs */
    }
    /* Center text within input fields */
    #modal-add-user-form input[type="text"],
    #modal-add-user-form input[type="password"] {
        text-align: center;
    }
    #modal-add-user-form label {
        display: block;
        margin-bottom: 5px; /* Consistent spacing below labels */
        font-weight: bold;
    }
    #modal-add-user-form button {
        padding: 10px 15px;
        background-color: #28a745; /* Keep green for add action */
        color: white;
        border: none;
        cursor: pointer;
        border-radius: 4px;
        width: 100%; /* Make button full width */
        margin-top: 10px; /* Add space above button */
    }
    #modal-add-user-form button:hover { background-color: #218838; }


    #manage-users {
        display: flex; /* Arrange role groups horizontally */
        gap: 20px; /* Add space between the lists */
        flex-wrap: wrap; /* Allow wrapping on smaller screens if needed */
    }
    #manage-users .user-role-group {
        /* Apply common box style */
    }
    #manage-users h5 { border-bottom: 1px solid #ddd; padding-bottom: 5px; margin-bottom: 10px; } /* Slightly darker border */

    /* Add User/Item Button Styling */
    .add-user-button,
    .add-item-button {
        display: block; /* Make it block level */
        width: calc(100% - 30px); /* Full width minus padding */
        margin: 15px auto 0; /* Center it with top margin */
        padding: 8px 12px;
        background-color: #007bff; /* Blue for add */
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        text-align: center;
        font-size: 14px;
    }
    .add-user-button:hover,
    .add-item-button:hover {
         background-color: #0056b3;
    }

    /* Modal Styling */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 100; /* Sit on top */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    }
    .modal-content {
        background-color: #fefefe;
        margin: 10% auto; /* 10% from the top and centered */
        padding: 20px;
        border: 1px solid #888;
        width: 80%; /* Could be more specific */
        max-width: 500px; /* Max width */
        border-radius: 5px;
        position: relative;
    }
    .close-button {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 28px;
        font-weight: bold;
    }
    .close-button:hover,
    .close-button:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
    /* Style form inside modal */
    #modal-add-user-form h4 { margin-top: 0; }

</style>
@endpush

@section('content')

    <h2>Admin Configurator Panel</h2>
    <hr>

    <section id="places-visits">
        <h3>Places & Visit Types</h3>
        <div class="admin-lists-container">
            <div class="admin-list admin-list-box"> <!-- Added common class -->
                <h4>Registered Places</h4>
                @if ($places->isNotEmpty())
                    <ul class="item-list"> <!-- Added class -->
                        @foreach ($places as $place)
                            <li class="list-item-with-actions"> <!-- Changed class -->
                                <span class="item-name">{{ $place->name }}</span>
                                <div class="action-buttons">
                                    {{-- Use Bootstrap button classes --}}
                                    <a href="{{ route('admin.places.edit', $place) }}" class="btn btn-sm btn-primary">Edit</a>
                                    {{-- Use DELETE route for remove --}}
                                    <form action="{{ route('admin.places.destroy', $place) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE') {{-- Method spoofing for DELETE request --}}
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No places found.</p>
                @endif
                 {{-- Link to create route --}}
                 <a href="{{ route('admin.places.create') }}" class="btn btn-primary btn-sm mt-3 add-item-button" style="text-decoration: none;">+ Add Place</a> {{-- Use link styled as button --}}
            </div>
            <div class="admin-list admin-list-box"> <!-- Added common class -->
                <h4>Registered Visit Types</h4>
                 @if ($visit_types->isNotEmpty())
                    <ul class="item-list"> <!-- Added class -->
                        @foreach ($visit_types as $visit_type)
                             <li class="list-item-with-actions"> <!-- Changed class -->
                                <span class="item-name">{{ $visit_type->title }}</span>
                                <div class="action-buttons">
                                     {{-- Use Bootstrap button classes --}}
                                    <a href="{{ route('admin.visit-types.edit', $visit_type) }}" class="btn btn-sm btn-primary">Edit</a>
                                     {{-- Use DELETE route for remove --}}
                                    <form action="{{ route('admin.visit-types.destroy', $visit_type) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE') {{-- Method spoofing for DELETE request --}}
                                        <button type="submit" class="btn btn-sm btn-danger">Remove</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p>No visit types found.</p>
                @endif
                 {{-- Link to create route --}}
                 <a href="{{ route('admin.visit-types.create') }}" class="btn btn-primary btn-sm mt-3 add-item-button" style="text-decoration: none;">+ Add Visit Type</a> {{-- Use link styled as button --}}
            </div>
        </div>
    </section>

    <hr>

    <section id="people-management">
        <h3>People Management</h3>

        <!-- Modal Structure -->
        <div id="addUserModal" class="modal">
            <div class="modal-content">
                <span class="close-button">&times;</span>
                <h4>Add New User</h4>
                <form id="modal-add-user-form" action="{{ route('admin.users.add') }}" method="POST">
                    @csrf
                    <div>
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="{{ old('username') }}" required>
                    </div>
                    <div>
                        <label for="password">Password:</label>
                        <input type="password" id="password" name="password" required minlength="6">
                    </div>
                    <div>
                        <label for="password_confirmation">Confirm Password:</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required>
                    </div>
                    <div>
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="">-- Select Role --</option>
                            <option value="volunteer" {{ old('role') == 'volunteer' ? 'selected' : '' }}>Volunteer</option>
                            <option value="configurator" {{ old('role') == 'configurator' ? 'selected' : '' }}>Configurator</option>
                        </select>
                    </div>
                    <div>
                        <button type="submit">Add User</button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Modal Structure -->

        <h4>Manage Existing Users</h4>
        <div id="manage-users">
            {{-- Group users by role (passed from controller) --}}
            @foreach ($users_by_role as $role => $users_in_role)
                <div class="user-role-group admin-list-box"> <!-- Added common class -->
                    <h5>{{ ucfirst($role) }}s</h5>
                    @if ($users_in_role->isNotEmpty())
                        <ul>
                            @foreach ($users_in_role as $user)
                                <li class="user-list-item list-item-with-actions"> <!-- Added classes -->
                                    <span class="username">{{ $user->username }}</span>
                                    <div class="action-buttons">
                                        {{-- Add Edit button for Volunteers (Placeholder) --}}
                                        @if ($role === 'volunteer')
                                             <a href="#" class="btn btn-sm btn-primary">Edit</a> {{-- Use Bootstrap classes --}}
                                        @endif

                                        {{-- Only show remove button for non-configurators and not the current user --}}
                                        @if ($role !== 'configurator' && $user->user_id !== Auth::id())
                                            {{-- Point to the new DELETE route --}}
                                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE') {{-- Method spoofing for DELETE request --}}
                                                <button type="submit" class="btn btn-sm btn-danger"> {{-- Use Bootstrap classes --}}
                                                    Remove
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p>No users found for this role.</p>
                    @endif
                    {{-- Only show Add button for roles other than Fruitore --}}
                    @if ($role !== 'fruitore')
                        <button class="add-user-button" data-role="{{ $role }}">+ Add {{ ucfirst($role) }}</button>
                    @endif
                </div>
            @endforeach
             @if(empty($users_by_role)) {{-- Check if the main array is empty (e.g., fetch error) --}}
                 <p>Could not load user list.</p>
             @endif
        </div>
    </section>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('addUserModal');
    const closeButton = modal.querySelector('.close-button');
    const addUserButtons = document.querySelectorAll('.add-user-button');
    const roleSelect = modal.querySelector('#role'); // Get the select element inside the modal
    const modalForm = document.getElementById('modal-add-user-form');

    // Function to open modal and pre-select role
    function openModal(role) {
        // Reset form fields (optional)
        modalForm.reset();
        // Pre-select the role
        if (roleSelect && role) {
            roleSelect.value = role;
        }
        modal.style.display = 'block';
    }

    // Function to close modal
    function closeModal() {
        modal.style.display = 'none';
    }

    // Add event listeners to all "Add User" buttons
    addUserButtons.forEach(button => {
        button.addEventListener('click', function() {
            const role = this.getAttribute('data-role');
            openModal(role);
        });
    });

    // Add event listener to close button
    if (closeButton) {
        closeButton.addEventListener('click', closeModal);
    }

    // Add event listener to close modal if user clicks outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });
});
</script>
@endpush