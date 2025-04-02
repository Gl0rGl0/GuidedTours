<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guided Tours</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div id="branding">
                <h1><a href="index.php">Guided Tours Org</a></h1>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php?page=home">Home</a></li>
                    <!-- Removed Available Tours link -->
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['role'] === 'configurator'): ?>
                            <li><a href="index.php?page=admin_configurator">Admin Panel</a></li>
                        <?php endif; ?>
                        <!-- User Dropdown -->
                        <li class="user-menu">
                            <a href="#" class="username-trigger"><?php echo htmlspecialchars($_SESSION['username']); ?> <span class="arrow">&#9662;</span></a>
                            <ul class="dropdown-content">
                                <li><a href="index.php?page=profile">Profile</a></li>
                                <li><a href="index.php?page=change_password">Change Password</a></li>
                                <li><a href="index.php?action=logout">Logout</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a href="index.php?page=login">Login</a></li>
                        <li><a href="index.php?page=register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <!-- Main content starts here -->
