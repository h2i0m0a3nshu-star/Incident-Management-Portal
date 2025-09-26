<?php
session_start();
require_once("db.php");

$incidents = array();
$clients = array();
$openCount = 0;
$inprogressCount = 0;
$resolvedCount = 0;
$error = array();
$flag = FALSE;
    
if( !$flag) {
    try {
        $conn =new mysqli($db_host, $db_user, $db_pwd, $db_db);

        if ($conn->connect_error) {
            throw new mysqli_sql_exception($conn->connect_error, $conn->connect_errno);
        }
    }

    catch (mysqli_sql_exception $e) {
        $error["Database Connection"]="Could not connect to the database.";
        $flag =TRUE;
    }

    $query ="SELECT 
        incidents.incidentID, 
        incidents.title, 
        CONCAT(clients.firstName, ' ', clients.lastName) AS clientName,
        CONCAT(users.firstName, ' ', users.lastName) AS assignedUser,
        incidents.severity, 
        incidents.status, 
        incidents.datecreated
    FROM incidents
    JOIN users ON users.userID = incidents.userID
    JOIN clients ON clients.clientID = incidents.clientID
    ORDER BY incidents.datecreated DESC
    LIMIT 5
    ";

    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $incidents = $result->fetch_all(MYSQLI_ASSOC);

    if( !$incidents) {
        $error["Database Error"]="Could not retrieve user information";
    }

    $query = "SELECT
        COUNT(incidentID) AS 'Open'
    FROM incidents
    WHERE status='open'
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $openCount = $row['Open'];

    $query = "SELECT
        COUNT(incidentID) AS 'In Progress'
    FROM incidents
    WHERE status='inprogress'
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $inprogressCount = $row['In Progress'];

    $query = "SELECT
        COUNT(incidentID) AS 'Resolved'
    FROM incidents
    WHERE status='resolved'
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $resolvedCount = $row['Resolved'];

    $conn->close();
}
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
        <nav class="active">
            Dashboard
        </nav>
        <nav>
            <a href="incidents.html">Incidents</a>
        </nav>
        <nav>
            <a href="clients.html">Clients</a>
        </nav>
        <?php if($_SESSION["role"] == "admin"): ?>
        <nav>
            <a href="users.html">Users</a>
        </nav>
        <?php endif; ?>
    </aside>

    <div id="main-content">
        <!-- Header -->
        <header id="dashboard-header">
            <h1>Dashboard</h1>
            <nav>
                <a id="new-incident-button" href="newincident.html">New Incident</a>
            </nav>
        </header>

        <!-- Main Body -->
        <main id="dashboard-main">
            <section id="overview-section">
                <h2>Overview</h2>
                <div class="card-container">
                    <div class="card">Open <span class="card-value">
                        <?php echo $openCount ?>
                    </span></div>
                    <div class="card">In Progress <span class="card-value">
                        <?php echo $inprogressCount ?>
                    </span></div>
                    <div class="card">Resolved <span class="card-value">
                        <?php echo $resolvedCount ?>
                    </span></div>
                    <div class="card">Total <span class="card-value">
                        <?php echo $openCount + $inprogressCount + $resolvedCount ?>
                    </span></div>
                </div>
            </section>

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
                    <?php if(!empty($incidents)): ?>
                    <tbody>

                        <?php foreach ($incidents as $incident): ?>
                        <tr>
                            <td><?php echo "#" . str_pad($incident['incidentID'], 4, "0", STR_PAD_LEFT); ?></td>
                            <td><?php echo htmlspecialchars($incident['title']); ?></td>
                            <td><?php echo htmlspecialchars($incident['clientName']); ?></td>
                            <td><?php echo htmlspecialchars($incident['assignedUser']); ?></td>
                            <td>
                                <span class="tag <?php echo strtolower($incident['severity']) ?>">
                                    <?php echo htmlspecialchars($incident['severity']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="tag <?php echo strtolower($incident['status']) ?>">
                                    <?php echo htmlspecialchars($incident['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($incident['datecreated']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <?php else: ?>
                    <tbody>
                        <tr>
                            <td colspan="7">No incidents found.</td>
                        </tr>
                    </tbody>
                    <?php endif; ?>
                </table>
            </section>
        </main>
    </div>
</body>

</html>