<h2>Login</h2>
<p>Please enter your credentials to access your dashboard.</p>

<?php
// Display login error message if it exists (passed from index.php)
if (!empty($login_error)) {
    echo '<p style="color: red; font-weight: bold;">' . htmlspecialchars($login_error) . '</p>';
}
?>

<form action="index.php" method="post">
    <input type="hidden" name="action" value="login"> <!-- Add hidden field for action -->
    <div>
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div>
        <button type="submit">Login</button>
    </div>
</form>
<p>Don't have an account? <a href="index.php?page=register">Register here</a> (Users/Fruitori only)</p>
<!-- TODO: Implement login logic (authentication, session handling) -->
<!-- TODO: Create registration page/logic -->
