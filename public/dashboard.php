<?php
session_start();
require_once("db.php");

// Initialize variables
$incidents = [];
$openCount = $inprogressCount = $resolvedCount = 0;
$error = [];

// Database connection
try {
    $conn = new mysqli($db_host, $db_user, $db_pwd, $db_db);
    if ($conn->connect_error) {
        throw new mysqli_sql_exception($conn->connect_error, $conn->connect_errno);
    }
} catch (mysqli_sql_exception $e) {
    $error["Database Connection"] = "Could not connect to the database.";
}

// Fetch recent incidents (latest 5)
$query = "
    SELECT 
        i.incidentID, 
        i.title, 
        CONCAT(c.firstName, ' ', c.lastName) AS clientName,
        CONCAT(u.firstName, ' ', u.lastName) AS assignedUser,
        i.severity, 
        i.status, 
        i.datecreated
    FROM incidents i
    JOIN users u   ON u.userID = i.userID
    JOIN clients c ON c.clientID = i.clientID
    ORDER BY i.datecreated DESC
    LIMIT 5
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$incidents = $result->fetch_all(MYSQLI_ASSOC);

// Function to fetch status counts
function getIncidentCount($conn, $status) {
    $stmt = $conn->prepare("SELECT COUNT(incidentID) AS count FROM incidents WHERE status=?");
    $stmt->bind_param("s", $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] ?? 0;
}

// Fetch counts for overview cards
$openCount        = getIncidentCount($conn, 'open');
$inprogressCount  = getIncidentCount($conn, 'inprogress');
$resolvedCount    = getIncidentCount($conn, 'resolved');

$conn->close();
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8" />
    <title>Dashboard</title>
    <link rel="stylesheet" href="css/styles.css" />
    <script src="js/eventhandler.js" defer></script>
</head>
<body>
    <!-- Sidebar -->
    <aside id="sidebar">
        <nav class="active">Dashboard</nav>
        <nav><a href="incidents.php">Incidents</a></nav>
        <nav><a href="clients.php">Clients</a></nav>
        <nav><a href="users.php">Users</a></nav>
    
    </aside>

    <div id="main-content">
        <!-- Header -->
        <header id="dashboard-header">
            <h1>Dashboard</h1>
            <nav>
                <a id="new-incident-button" href="newincident.html">+ New Incident</a>
            </nav>
        </header>

        <!-- Main Body -->
        <main id="dashboard-main">
            
            <!-- Overview Cards -->
            <section id="overview-section">
                <h2>Overview</h2>
                <div class="card-container">
                    <div class="card">Open <span class="card-value"><?= $openCount ?></span></div>
                    <div class="card">In Progress <span class="card-value"><?= $inprogressCount ?></span></div>
                    <div class="card">Resolved <span class="card-value"><?= $resolvedCount ?></span></div>
                    <div class="card">Total <span class="card-value"><?= $openCount + $inprogressCount + $resolvedCount ?></span></div>
                </div>
            </section>

            <!-- Recent Incidents Table -->
            <section id="recent-incidents">
                <h2>Recent Incidents</h2>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Client</th>
                            <th>Assigned User</th>
                            <th>Severity</th>
                            <th>Status</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($incidents)): ?>
                            <?php foreach ($incidents as $incident): ?>
                                <tr>
                                    <td><?= "#" . str_pad($incident['incidentID'], 4, "0", STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($incident['title']) ?></td>
                                    <td><?= htmlspecialchars($incident['clientName']) ?></td>
                                    <td><?= htmlspecialchars($incident['assignedUser']) ?></td>
                                    <td>
                                        <span class="tag <?= strtolower($incident['severity']) ?>">
                                            <?= htmlspecialchars($incident['severity']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="tag <?= strtolower($incident['status']) ?>">
                                            <?= htmlspecialchars($incident['status']) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($incident['datecreated']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7">No incidents found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
