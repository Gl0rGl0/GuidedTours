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
    .action-button { /* Common button styles */
        color: white;
        border: none;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
        line-height: 1;
        white-space: nowrap;
    }
    .edit-button {
        background-color: #007bff; /* Blue for edit */
    }
    .edit-button:hover {
        background-color: #0056b3;
        color: white;
        text-decoration: none;
    }
    .remove-button, /* Style for Places/Visits remove */
    .remove-user-button { /* Style for User remove */
        background-color: #dc3545; /* Red background */
        color: white;
        border: none;
        padding: 3px 8px;
        border-radius: 4px;
        cursor: pointer;
        text-decoration: none;
        font-size: 12px;
        line-height: 1;
        /* margin-left: 10px; */ /* Removed fixed margin */
        white-space: nowrap;
    }
     .remove-button:hover,
     .remove-user-button:hover {
        background-color: #c82333; /* Darker red on hover */
        color: white;
        text-decoration: none;
    }


    #add-user-form { background-color: #f9f9f9; padding: 15px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 20px; max-width: 400px;}
    #add-user-form div { margin-bottom: 10px; }
    #add-user-form label { display: block; margin-bottom: 3px; font-weight: bold; }
    #add-user-form input[type="text"],
    #modal-add-user-form input[type="text"],
    /* #modal-add-user-form input[type="email"], */ /* Email removed */
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
    /* #modal-add-user-form input[type="email"], */ /* Email removed */
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

    /* Toast Notification Styling */
    #toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050; /* Ensure it's above most elements */
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }
    .toast {
        background-color: #333;
        color: #fff;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 5px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.5s, visibility 0.5s, transform 0.5s;
        transform: translateX(100%); /* Start off-screen */
        min-width: 250px;
        max-width: 400px;
    }
    .toast.show {
        opacity: 1;
        visibility: visible;
        transform: translateX(0); /* Slide in */
    }
    .toast.success {
        background-color: #28a745; /* Green for success */
    }
    .toast.error {
        background-color: #dc3545; /* Red for error */
    }
</style>
@endpush

@section('content')
    <!-- Toast Notification Container -->
    <div id="toast-container"></div>

    <h2>Admin Configurator Panel</h2>

    {{-- Removed static session message display. JS toasts will handle feedback. --}}

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
                                    {{-- Link to edit route --}}
                                    <a href="{{ route('admin.places.edit', $place) }}" class="edit-button action-button">Edit</a>
                                    {{-- Use POST route for remove --}}
                                    <form action="{{ route('admin.places.remove', $place) }}" method="POST" style="display:inline;">
                                        @csrf
                                        {{-- @method('DELETE') Removed --}}
                                        <button type="submit" class="remove-button action-button">Remove</button> {{-- Removed onclick --}}
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
                                    {{-- Link to edit route --}}
                                    <a href="{{ route('admin.visit-types.edit', $visit_type) }}" class="edit-button action-button">Edit</a>
                                     {{-- Use POST route for remove --}}
                                    <form action="{{ route('admin.visit-types.remove', $visit_type) }}" method="POST" style="display:inline;">
                                        @csrf
                                        {{-- @method('DELETE') Removed --}}
                                        <button type="submit" class="remove-button action-button">Remove</button> {{-- Removed onclick --}}
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
                    {{-- Email removed --}}
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
                            <option value="fruitore" {{ old('role') == 'fruitore' ? 'selected' : '' }}>Fruitore (User)</option>
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
                                             <a href="#" class="edit-button action-button">Edit</a>
                                        @endif

                                        {{-- Only show remove button for non-configurators and not the current user --}}
                                        @if ($role !== 'configurator' && $user->user_id !== Auth::id())
                                            {{-- Point to the new POST route and remove @method('DELETE') --}}
                                            <form action="{{ route('admin.users.remove', $user) }}" method="POST" style="display:inline;">
                                                @csrf
                                                {{-- @method('DELETE') Removed --}}
                                                <button type="submit"
                                                       class="remove-user-button action-button">
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
                    <button class="add-user-button" data-role="{{ $role }}">+ Add {{ ucfirst($role) }}</button>
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

    // Toast Notification Logic
    const toastContainer = document.getElementById('toast-container');

    function showToast(text, type = 'info') { // type can be 'success', 'error', 'info'
        if (!text || !toastContainer) return;

        const toast = document.createElement('div');
        toast.className = `toast ${type}`; // Add type class for styling
        toast.textContent = text;

        toastContainer.appendChild(toast);

        // Trigger reflow to enable transition
        requestAnimationFrame(() => {
            requestAnimationFrame(() => {
                 toast.classList.add('show');
            });
        });


        // Set timeout to hide and remove toast
        const hideTimeout = setTimeout(() => {
            toast.classList.remove('show');
            // Remove the element after the transition completes
            toast.addEventListener('transitionend', () => {
                 if (toast.parentNode === toastContainer) { // Check if still attached
                    toastContainer.removeChild(toast);
                 }
            }, { once: true }); // Ensure listener runs only once
             // Fallback removal if transitionend doesn't fire (e.g., element removed early)
             setTimeout(() => {
                 if (toast.parentNode === toastContainer) {
                    toastContainer.removeChild(toast);
                 }
             }, 600); // 600ms should be enough for the 0.5s transition

        }, 5000); // 5 seconds display time
    }

    // Show toasts based on Laravel Session Flash Data
    @if (session('status'))
        showToast("{{ session('status') }}", 'success');
    @endif

    // Show first validation error in a toast
    @if ($errors->any())
        showToast("{{ $errors->first() }}", 'error');
    @endif

});
</script>
@endpush
