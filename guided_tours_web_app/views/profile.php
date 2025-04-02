<?php
// Placeholder for Profile Page
// Ensure user is logged in (handled by index.php routing)
if (!isset($_SESSION['user_id'])) {
    // This should technically not be reached if index.php logic is correct
    header('Location: index.php?page=login');
    exit;
}
?>

<h2>User Profile</h2>
<p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
<p>This page will allow you to view and potentially edit your profile information.</p>
<p><em>(Functionality to be implemented)</em></p>

<!-- Example: Display user role -->
<p>Your current role: <?php echo htmlspecialchars($_SESSION['role']); ?></p>

<!-- TODO: Add form to edit profile details if required -->
