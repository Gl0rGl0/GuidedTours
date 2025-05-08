<?php
// Placeholder for Change Password Page
// Ensure user is logged in (handled by index.php routing)
if (!isset($_SESSION['user_id'])) {
    // This should technically not be reached if index.php logic is correct
    header('Location: index.php?page=login');
    exit;
}

// Messages are now handled by index.php and passed either via GET or set directly
// $change_pwd_message and $change_pwd_success are set by index.php if POST fails without redirect
// Check for success message from GET parameter after redirect
if (isset($_GET['message']) && $_GET['message'] === 'success') {
    $change_pwd_message = 'Password updated successfully!';
    $change_pwd_success = true; // Assume success if message=success is present
}

?>

<h2>Change Password</h2>

<?php if (!empty($change_pwd_message)): // Check if the message variable is set (either by GET or failed POST) ?>
    <p class="<?php echo $change_pwd_success ? 'success-message' : 'error-message'; ?>">
        <?php echo htmlspecialchars($change_pwd_message); ?>
    </p>
<?php endif; ?>

<form action="index.php" method="POST"> <?php // Action points to index.php now ?>
    <input type="hidden" name="action" value="change_password">
    <input type="hidden" name="page" value="change_password"> <?php // Keep track of the page for index.php routing ?>
    <div>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
    </div>
    <div>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required minlength="6">
    </div>
    <div>
        <label for="confirm_new_password">Confirm New Password:</label>
        <input type="password" id="confirm_new_password" name="confirm_new_password" required>
    </div>
    <div>
        <button type="submit">Change Password</button>
    </div>
</form>

<p><em>(Functionality to be fully implemented)</em></p>

<style>
    /* Basic form styling - consider moving to style.css */
    form div { margin-bottom: 10px; }
    label { display: block; margin-bottom: 5px; }
    input[type="password"] { width: 250px; padding: 8px; }
    button { padding: 10px 15px; background-color: #333; color: white; border: none; cursor: pointer; }
    button:hover { background-color: #555; }
    .error-message { color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px; }
    .success-message { color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px; }
</style>
