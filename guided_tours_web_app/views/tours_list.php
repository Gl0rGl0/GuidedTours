<?php
require_once __DIR__ . '/../includes/db_connect.php'; // Establishes $pdo connection

$available_tours = [];
$error_message = '';

try {
    // Fetch proposed and confirmed visits along with their type and place details
    // Only show visits scheduled for today or in the future
    $sql = "SELECT
                v.visit_id,
                v.visit_date,
                v.status,
                vt.title AS visit_type_title,
                vt.description AS visit_type_description,
                vt.meeting_point,
                vt.start_time,
                vt.duration_minutes,
                vt.requires_ticket,
                p.name AS place_name,
                p.location AS place_location
            FROM visits v
            JOIN visit_types vt ON v.visit_type_id = vt.visit_type_id
            JOIN places p ON vt.place_id = p.place_id
            WHERE v.status IN ('proposed', 'confirmed')
              AND v.visit_date >= CURDATE()
            ORDER BY v.visit_date, vt.start_time";

    $stmt = $pdo->query($sql);
    $available_tours = $stmt->fetchAll();

} catch (\PDOException $e) {
    error_log("Error fetching available tours: " . $e->getMessage());
    $error_message = "Sorry, we couldn't retrieve the tour list at this time.";
}

?>

<h2>Available Tours</h2>

<?php if ($error_message): ?>
    <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
<?php elseif (empty($available_tours)): ?>
    <p>There are currently no available tours scheduled. Please check back later!</p>
<?php else: ?>
    <p>Here are the upcoming guided tours available for registration or viewing:</p>
    <style>
        /* Basic styling for the tour list */
        .tour-list { list-style: none; padding: 0; }
        .tour-item { background: #fff; border: 1px solid #ddd; margin-bottom: 15px; padding: 15px; border-radius: 5px; }
        .tour-item h3 { margin-top: 0; color: #0779e4; }
        .tour-item strong { color: #333; }
        .tour-status { font-weight: bold; padding: 3px 8px; border-radius: 3px; color: #fff; }
        .status-proposed { background-color: #ffc107; color: #333; } /* Yellow */
        .status-confirmed { background-color: #28a745; } /* Green */
    </style>
    <ul class="tour-list">
        <?php foreach ($available_tours as $tour): ?>
            <li class="tour-item">
                <h3><?php echo htmlspecialchars($tour['visit_type_title']); ?></h3>
                <p>
                    <span class="tour-status status-<?php echo htmlspecialchars($tour['status']); ?>">
                        <?php echo htmlspecialchars(ucfirst($tour['status'])); ?>
                    </span>
                </p>
                <p><strong>Place:</strong> <?php echo htmlspecialchars($tour['place_name']); ?> (<?php echo htmlspecialchars($tour['place_location']); ?>)</p>
                <p><strong>Date:</strong> <?php echo htmlspecialchars(date('D, M j, Y', strtotime($tour['visit_date']))); ?></p>
                <p><strong>Time:</strong> <?php echo htmlspecialchars(date('g:i A', strtotime($tour['start_time']))); ?> (Duration: <?php echo htmlspecialchars($tour['duration_minutes']); ?> mins)</p>
                <p><strong>Meeting Point:</strong> <?php echo htmlspecialchars($tour['meeting_point']); ?></p>
                <p><?php echo nl2br(htmlspecialchars($tour['visit_type_description'])); ?></p>
                <?php if ($tour['requires_ticket']): ?>
                    <p><em>Note: An entrance ticket purchase may be required at the venue.</em></p>
                <?php endif; ?>
                <?php if ($tour['status'] === 'proposed'): ?>
                    <p><a href="index.php?page=register_tour&visit_id=<?php echo $tour['visit_id']; ?>">Register for this Tour</a></p>
                    <!-- TODO: Implement registration page/logic -->
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
