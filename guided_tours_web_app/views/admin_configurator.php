<?php
// Admin Configurator Page
// Ensure user is logged in and is a configurator (double-check, index.php should handle primary check)
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'configurator') {
    // Redirect if not authorized
    header('Location: index.php?page=home&error=unauthorized_admin');
    exit;
}

// Include DB connection if not already included (should be by index.php)
if (!isset($pdo)) {
    require_once __DIR__ . '/../includes/db_connect.php';
}

// --- Data Fetching ---
$places = [];
$visit_types = [];
$users_by_role = ['configurator' => [], 'volunteer' => [], 'fruitore' => []];
$fetch_error = '';

try {
    // Fetch Places with IDs
    $stmt_places = $pdo->query("SELECT place_id, name FROM places ORDER BY name");
    $places = $stmt_places->fetchAll(PDO::FETCH_ASSOC); // Fetch associative array

    // Fetch Visit Types with IDs
    $stmt_visit_types = $pdo->query("SELECT visit_type_id, title FROM visit_types ORDER BY title");
    $visit_types = $stmt_visit_types->fetchAll(PDO::FETCH_ASSOC); // Fetch associative array

    // Fetch Users (already fetches ID)
    $stmt_users = $pdo->query("SELECT user_id, username, role FROM users ORDER BY role, username");
    while ($user = $stmt_users->fetch(PDO::FETCH_ASSOC)) {
        if (array_key_exists($user['role'], $users_by_role)) {
            $users_by_role[$user['role']][] = $user;
        }
    }

} catch (\PDOException $e) {
    error_log("Admin Configurator Fetch Error: " . $e->getMessage());
    $fetch_error = "An error occurred while fetching data for the admin panel.";
}

// --- Message Handling (from GET params or failed POST in index.php) ---
$message = '';
$error = '';
// Messages from redirects in index.php
if (isset($_GET['message'])) {
    switch ($_GET['message']) {
        case 'user_added': $message = 'User added successfully!'; break;
        case 'user_removed': $message = 'User removed successfully!'; break;
    }
}
if (isset($_GET['error'])) {
     switch ($_GET['error']) {
        case 'invalid_user_id': $error = 'Invalid user ID specified for removal.'; break;
        case 'cannot_remove_self': $error = 'You cannot remove your own account.'; break;
        case 'cannot_remove_configurator': $error = 'Configurator users cannot be removed.'; break;
        case 'user_not_found': $error = 'User specified for removal not found.'; break;
        case 'remove_failed': $error = 'Failed to remove the user.'; break;
        case 'db_error': $error = 'A database error occurred during the operation.'; break;
        case 'unauthorized_admin': $error = 'Unauthorized access attempt detected.'; break; // From this page's check
    }
}
// Message from failed add user POST (passed via $admin_message from index.php)
if (!empty($admin_message) && !$admin_success) {
    $error = $admin_message; // Display the specific error from the add user attempt
}


?>

<!-- Toast Notification Container -->
<div id="toast-container"></div>

<h2>Admin Configurator Panel</h2>

<?php /* Static messages removed, handled by JS toasts now */ ?>
<?php if ($fetch_error): ?>
    <p class="admin-message error"><?php echo htmlspecialchars($fetch_error); ?></p> <?php // Keep fetch error static for now ?>
<?php endif; ?>

<hr>

<section id="places-visits">
    <h3>Places & Visit Types</h3>
    <div class="admin-lists-container">
        <div class="admin-list admin-list-box"> <!-- Added common class -->
            <h4>Registered Places</h4>
            <?php if (!empty($places)): ?>
                <ul class="item-list"> <!-- Added class -->
                    <?php foreach ($places as $place): ?>
                        <li class="list-item-with-actions"> <!-- Changed class -->
                            <span class="item-name"><?php echo htmlspecialchars($place['name']); ?></span>
                            <div class="action-buttons">
                                <a href="index.php?page=edit_place&id=<?php echo $place['place_id']; ?>" class="edit-button action-button">Edit</a> <!-- Added action-button class -->
                                <a href="index.php?action=remove_place&id=<?php echo $place['place_id']; ?>" class="remove-button action-button" onclick="return confirm('Are you sure you want to remove place \'<?php echo htmlspecialchars(addslashes($place['name'])); ?>\'?');">Remove</a> <!-- Added action-button class -->
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No places found.</p>
            <?php endif; ?>
             <button class="add-item-button" onclick="location.href='index.php?page=add_place'">+ Add Place</button> <!-- Added Add Button -->
        </div>
        <div class="admin-list admin-list-box"> <!-- Added common class -->
            <h4>Registered Visit Types</h4>
             <?php if (!empty($visit_types)): ?>
                <ul class="item-list"> <!-- Added class -->
                    <?php foreach ($visit_types as $visit_type): ?>
                         <li class="list-item-with-actions"> <!-- Changed class -->
                            <span class="item-name"><?php echo htmlspecialchars($visit_type['title']); ?></span>
                            <div class="action-buttons">
                                <a href="index.php?page=edit_visit_type&id=<?php echo $visit_type['visit_type_id']; ?>" class="edit-button action-button">Edit</a> <!-- Added action-button class -->
                                <a href="index.php?action=remove_visit_type&id=<?php echo $visit_type['visit_type_id']; ?>" class="remove-button action-button" onclick="return confirm('Are you sure you want to remove visit type \'<?php echo htmlspecialchars(addslashes($visit_type['title'])); ?>\'?');">Remove</a> <!-- Added action-button class -->
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No visit types found.</p>
            <?php endif; ?>
             <button class="add-item-button" onclick="location.href='index.php?page=add_visit_type'">+ Add Visit Type</button> <!-- Added Add Button -->
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
            <form id="modal-add-user-form" action="index.php" method="POST">
                <input type="hidden" name="action" value="add_user">
            <div>
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
             <!-- Removed Email Field -->
            <div>
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required minlength="6">
            </div>
            <div>
                <label for="password_confirm">Confirm Password:</label>
                <input type="password" id="password_confirm" name="password_confirm" required>
            </div>
            <div>
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="">-- Select Role --</option>
                    <option value="fruitore">Fruitore (User)</option>
                    <option value="volunteer">Volunteer</option>
                    <option value="configurator">Configurator</option>
                </select>
            </div>
            <div>
                <button type="submit">Add User</button>
            </div>
        </form>
            </form>
        </div>
    </div>
    <!-- End Modal Structure -->

    <h4>Manage Existing Users</h4>
    <div id="manage-users">
        <?php foreach ($users_by_role as $role => $users): ?>
            <div class="user-role-group admin-list-box"> <!-- Added common class -->
                <h5><?php echo htmlspecialchars(ucfirst($role)) . 's'; ?></h5>
                <?php if (!empty($users)): ?>
                    <ul>
                        <?php foreach ($users as $user): ?>
                            <li class="user-list-item list-item-with-actions"> <!-- Added classes -->
                                <span class="username"><?php echo htmlspecialchars($user['username']); ?></span>
                                <div class="action-buttons">
                                    <?php // Add Edit button for Volunteers ?>
                                    <?php if ($role === 'volunteer'): ?>
                                         <a href="index.php?page=edit_user&id=<?php echo $user['user_id']; ?>" class="edit-button action-button">Edit</a> <!-- Added action-button class -->
                                    <?php endif; ?>

                                    <?php // Only show remove button for non-configurators and not the current user ?>
                                    <?php if ($role !== 'configurator' && $user['user_id'] !== $_SESSION['user_id']): ?>
                                        <a href="index.php?action=remove_user&id=<?php echo $user['user_id']; ?>"
                                           class="remove-user-button action-button"
                                           onclick="return confirm('Are you sure you want to remove user \'<?php echo htmlspecialchars(addslashes($user['username'])); ?>\'? This action cannot be undone.');">
                                            Remove
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No users found for this role.</p>
                <?php endif; ?>
                <button class="add-user-button" data-role="<?php echo htmlspecialchars($role); ?>">+ Add <?php echo htmlspecialchars(ucfirst($role)); ?></button>
            </div>
        <?php endforeach; ?>
         <?php if ($fetch_error): ?>
             <p>Could not load user list due to a database error.</p>
         <?php endif; ?>
    </div>
</section>

<style>
    /* Basic Admin Panel Styling - Consider moving to style.css */
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
        margin-left: 10px; /* Keep space for user remove */
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
    #modal-add-user-form input[type="email"],
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
    #modal-add-user-form input[type="email"],
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
    closeButton.addEventListener('click', closeModal);

    // Add event listener to close modal if user clicks outside the modal content
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            closeModal();
        }
    });

    // Toast Notification Logic
    const toastContainer = document.getElementById('toast-container');
    // Read messages passed from PHP. Use json_encode for safety.
    const phpMessage = <?php echo json_encode($message ?? ''); ?>;
    const phpError = <?php echo json_encode($error ?? ''); ?>;

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

    // Show toasts based on PHP messages passed from URL parameters or failed POST
    if (phpMessage) {
        showToast(phpMessage, 'success');
    }
    if (phpError) {
        showToast(phpError, 'error');
    }

});
</script>
