<?php
// Start the session at the very beginning
session_start();

// Include database connection potentially needed for actions/views
require_once __DIR__ . '/includes/db_connect.php'; // Establishes $pdo connection

// --- Login Action Handling ---
$login_error = '';
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($username) || empty($password)) {
        $login_error = 'Username and password are required.';
    } else {
        try {
            $sql = "SELECT user_id, username, password_hash, role FROM users WHERE username = :username";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch();

            // IMPORTANT: This relies on REAL password hashes in the database!
            if ($user && password_verify($password, $user['password_hash'])) {
                // Password is correct, start session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                // Regenerate session ID for security
                session_regenerate_id(true);
                // Redirect to home page or dashboard after successful login
                header('Location: index.php?page=home');
                exit; // Important to prevent further script execution after redirect
            } else {
                $login_error = 'Invalid username or password.';
            }
        } catch (\PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $login_error = 'An error occurred during login. Please try again later.';
        }
    }
    // If login failed, the script continues and will show the login page again with $login_error set
}

// --- Registration Action Handling ---
$registration_message = '';
$registration_success = false;
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');

    // Basic Validation
    if (empty($username) || empty($password) || empty($password_confirm)) {
        $registration_message = 'All fields are required.';
    } elseif ($password !== $password_confirm) {
        $registration_message = 'Passwords do not match.';
    } elseif (strlen($password) < 6) { // Example: Minimum password length
        $registration_message = 'Password must be at least 6 characters long.';
    } else {
        // Check if username already exists
        try {
            $sql_check = "SELECT user_id FROM users WHERE username = :username";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute(['username' => $username]);
            if ($stmt_check->fetch()) {
                $registration_message = 'Username already taken. Please choose another.';
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Insert new user (role 'fruitore' by default for registration form)
                $sql_insert = "INSERT INTO users (username, password_hash, role, first_login) VALUES (:username, :password_hash, 'fruitore', false)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->execute([
                    'username' => $username,
                    'password_hash' => $password_hash
                ]);

                if ($stmt_insert->rowCount() > 0) {
                    $registration_message = 'Registration successful! You can now log in.';
                    $registration_success = true;
                    // Optionally log the user in automatically here or redirect to login
                } else {
                    $registration_message = 'Registration failed. Please try again.';
                }
            }
        } catch (\PDOException $e) {
            error_log("Registration Error: " . $e->getMessage());
            $registration_message = 'An error occurred during registration. Please try again later.';
        }
    }
    // If registration failed or succeeded with message, script continues to show registration page again
    // We need to ensure the $page variable is set correctly if we stay on the registration page
    if (!$registration_success) {
         $_GET['page'] = 'register'; // Force page to register to show messages
    }
}

// --- Add User Action Handling (Admin Only) ---
$admin_message = ''; // Message for admin actions
$admin_success = false;
if (isset($_POST['action']) && $_POST['action'] === 'add_user') {
    // Security Check: Only configurators can add users
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurator') {
        // Redirect or show error if not authorized
        header('Location: index.php?page=home&error=unauthorized');
        exit;
    }

    $new_username = trim($_POST['username'] ?? '');
    $new_password = trim($_POST['password'] ?? '');
    $new_password_confirm = trim($_POST['password_confirm'] ?? '');
    $new_role = trim($_POST['role'] ?? '');
    $allowed_roles = ['configurator', 'volunteer', 'fruitore'];

    // Basic Validation
    if (empty($new_username) || empty($new_password) || empty($new_password_confirm) || empty($new_role)) {
        $admin_message = 'All fields are required for adding a user.';
    } elseif ($new_password !== $new_password_confirm) {
        $admin_message = 'Passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $admin_message = 'Password must be at least 6 characters long.';
    } elseif (!in_array($new_role, $allowed_roles)) {
        $admin_message = 'Invalid role selected.';
    } else {
        try {
            $sql_check = "SELECT user_id FROM users WHERE username = :username";
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute(['username' => $new_username]);
            if ($stmt_check->fetch()) {
                $admin_message = 'Username already taken.';
            } else {
                // Hash the password
                $password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $sql_insert = "INSERT INTO users (username, password_hash, role, first_login) VALUES (:username, :password_hash, :role, false)";
                $stmt_insert = $pdo->prepare($sql_insert);
                $stmt_insert->execute([
                    'username' => $new_username,
                    'password_hash' => $password_hash,
                    'role' => $new_role
                ]);

                if ($stmt_insert->rowCount() > 0) {
                    $admin_message = 'User added successfully!';
                    $admin_success = true;
                    // Redirect back to admin page with success message
                    header('Location: index.php?page=admin_configurator&message=user_added');
                    exit;
                } else {
                    $admin_message = 'Failed to add user. Please try again.';
                }
            }
        } catch (\PDOException $e) {
            error_log("Add User Error: " . $e->getMessage());
            $admin_message = 'An error occurred while adding the user.';
        }
    }
     // If adding failed, stay on admin page to show message
     $_GET['page'] = 'admin_configurator'; // Force page to show messages
}

// --- Remove User Action Handling (Admin Only) ---
if (isset($_GET['action']) && $_GET['action'] === 'remove_user' && isset($_GET['user_id'])) {
     // Security Check: Only configurators can remove users
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurator') {
        header('Location: index.php?page=home&error=unauthorized');
        exit;
    }

    $user_id_to_remove = filter_var($_GET['user_id'], FILTER_VALIDATE_INT);

    if ($user_id_to_remove === false || $user_id_to_remove <= 0) {
        // Invalid user ID format
         header('Location: index.php?page=admin_configurator&error=invalid_user_id');
         exit;
    }

    // Prevent configurator from removing themselves or other configurators
    if ($user_id_to_remove === $_SESSION['user_id']) {
        header('Location: index.php?page=admin_configurator&error=cannot_remove_self');
        exit;
    }

    try {
        // Check the role of the user being removed
        $sql_check_role = "SELECT role FROM users WHERE user_id = :user_id";
        $stmt_check_role = $pdo->prepare($sql_check_role);
        $stmt_check_role->execute(['user_id' => $user_id_to_remove]);
        $user_to_remove = $stmt_check_role->fetch();

        if (!$user_to_remove) {
             header('Location: index.php?page=admin_configurator&error=user_not_found');
             exit;
        }

        if ($user_to_remove['role'] === 'configurator') {
            header('Location: index.php?page=admin_configurator&error=cannot_remove_configurator');
            exit;
        }

        // Proceed with deletion for 'volunteer' or 'fruitore'
        $sql_delete = "DELETE FROM users WHERE user_id = :user_id";
        $stmt_delete = $pdo->prepare($sql_delete);
        $stmt_delete->execute(['user_id' => $user_id_to_remove]);

        if ($stmt_delete->rowCount() > 0) {
             header('Location: index.php?page=admin_configurator&message=user_removed');
             exit;
        } else {
             header('Location: index.php?page=admin_configurator&error=remove_failed');
             exit;
        }

    } catch (\PDOException $e) {
         error_log("Remove User Error: " . $e->getMessage());
         header('Location: index.php?page=admin_configurator&error=db_error');
         exit;
    }
}

// --- Change Password Action Handling ---
$change_pwd_message = ''; // Message for change password action
$change_pwd_success = false;
if (isset($_POST['action']) && $_POST['action'] === 'change_password') {
    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login&error=auth_required');
        exit;
    }

    $current_password = trim($_POST['current_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_new_password = trim($_POST['confirm_new_password'] ?? '');
    $user_id = $_SESSION['user_id'];

    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_new_password)) {
        $change_pwd_message = 'All password fields are required.';
    } elseif ($new_password !== $confirm_new_password) {
        $change_pwd_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $change_pwd_message = 'New password must be at least 6 characters long.';
    } else {
        try {
            // Fetch current password hash
            $sql_fetch = "SELECT password_hash FROM users WHERE user_id = :user_id";
            $stmt_fetch = $pdo->prepare($sql_fetch);
            $stmt_fetch->execute(['user_id' => $user_id]);
            $user = $stmt_fetch->fetch();

            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Current password is correct, hash and update the new one
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);

                $sql_update = "UPDATE users SET password_hash = :new_password_hash WHERE user_id = :user_id";
                $stmt_update = $pdo->prepare($sql_update);
                $stmt_update->execute([
                    'new_password_hash' => $new_password_hash,
                    'user_id' => $user_id
                ]);

                if ($stmt_update->rowCount() > 0) {
                    $change_pwd_message = 'Password updated successfully!';
                    $change_pwd_success = true;
                    // Redirect with success message
                    header('Location: index.php?page=change_password&message=success');
                    exit;
                } else {
                    $change_pwd_message = 'Failed to update password. Please try again.';
                }
            } else {
                // Invalid current password
                $change_pwd_message = 'Incorrect current password.';
            }
        } catch (\PDOException $e) {
            error_log("Change Password Error: " . $e->getMessage());
            $change_pwd_message = 'An error occurred while changing the password.';
        }
    }
    // If update failed, stay on page to show message
    $_GET['page'] = 'change_password'; // Force page to show messages
}


// --- Logout Action Handling ---
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_unset();     // Unset all session variables
    session_destroy();   // Destroy the session
    header('Location: index.php?page=home'); // Redirect to home page
    exit;
}


// --- Page Routing ---

// Include common header (moved after action handling)
include 'includes/header.php';

// Determine the page to load
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to home

// Basic security: sanitize page name (allow alphanumeric and underscore)
$page = preg_replace('/[^a-zA-Z0-9_]/', '', $page);

// Define allowed pages and their corresponding view files
$allowed_pages = [
    'home' => 'views/home.php',
    // 'tours' => 'views/tours_list.php', // Removed - tours are on home page now
    'login' => 'views/login_form.php',
    'register' => 'views/register_form.php',
    'register_tour' => 'views/register_tour_form.php', // Placeholder for tour registration
    'profile' => 'views/profile.php', // Placeholder
    'change_password' => 'views/change_password.php', // Placeholder
    'admin_configurator' => 'views/admin_configurator.php', // New admin page
    // TODO: Add other role-specific dashboards if needed
];

// Load the requested page if it exists, otherwise show home or an error
// Pass relevant messages to specific views (admin_message added)
if ($page === 'login' && file_exists($allowed_pages['login'])) {
     include $allowed_pages['login']; // $login_error is available
} elseif ($page === 'register' && file_exists($allowed_pages['register'])) {
     include $allowed_pages['register']; // $registration_message and $registration_success are available
} elseif ($page === 'change_password' && file_exists($allowed_pages['change_password'])) {
     // Security check: Ensure user is logged in
     if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login&redirect=change_password');
        exit;
     }
     // Pass change password messages if they exist (from failed POST)
     include $allowed_pages['change_password']; // $change_pwd_message, $change_pwd_success might be set
} elseif ($page === 'admin_configurator' && file_exists($allowed_pages['admin_configurator'])) {
    // Security check before including admin page
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'configurator') {
         echo "<h2>Access Denied</h2><p>You do not have permission to view this page.</p>";
    } else {
        // Pass admin messages if they exist (e.g., from failed add user attempt)
        include $allowed_pages['admin_configurator']; // $admin_message, $admin_success might be set
    }
} elseif (array_key_exists($page, $allowed_pages) && file_exists($allowed_pages[$page])) {
    // Check for protected pages if needed (e.g., profile, change_password require login)
    if (($page === 'profile' || $page === 'change_password') && !isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login&redirect=' . urlencode($page)); // Redirect to login if not logged in
        exit;
    }
    include $allowed_pages[$page];
} elseif ($page === 'home' && file_exists($allowed_pages['home'])) {
    // Fallback to home if default page exists
    include $allowed_pages['home'];
} else {
    // Page not found error
    echo "<h2>Page Not Found</h2>";
    echo "<p>Sorry, the page you requested could not be found.</p>";
    // Optionally include a specific 404 view: include 'views/404.php';
}

// Include common footer
include 'includes/footer.php';

?>
