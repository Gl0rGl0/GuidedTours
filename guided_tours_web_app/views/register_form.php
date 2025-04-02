<h2>Register as a New User (Fruitore)</h2>
<p>Create an account to register for guided tours.</p>

<?php
// Placeholder for registration error/success messages
if (!empty($registration_message)) {
    $message_color = ($registration_success ?? false) ? 'green' : 'red';
    echo '<p style="color: ' . $message_color . '; font-weight: bold;">' . htmlspecialchars($registration_message) . '</p>';
}
?>

<form action="index.php" method="post">
    <input type="hidden" name="action" value="register">
    <div>
        <label for="reg_username">Username:</label>
        <input type="text" id="reg_username" name="username" required>
    </div>
    <div>
        <label for="reg_password">Password:</label>
        <input type="password" id="reg_password" name="password" required>
    </div>
    <div>
        <label for="reg_password_confirm">Confirm Password:</label>
        <input type="password" id="reg_password_confirm" name="password_confirm" required>
    </div>
    <div>
        <button type="submit">Register</button>
    </div>
</form>
<p>Already have an account? <a href="index.php?page=login">Login here</a>.</p>
<!-- TODO: Implement registration logic in index.php -->
