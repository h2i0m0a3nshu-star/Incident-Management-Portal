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

$query = "SELECT incidents.incidentID, incidents.title, CONCAT(clients.firstName,' ',clients.lastName) AS Client, CONCAT(users.firstName, ' ', users.lastName) AS 'Assigned User', incidents.severity, incidents.status, incidents.datecreated
    FROM incidents
    JOIN clients ON clients.clientID = incidents.clientID
    JOIN users ON users.userID = incidents.userID;
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$incidents = $result->fetch_all(MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Incidents</title>
    <link rel="stylesheet" href="css/styles.css" />
    <script src="js/eventhandler.js" defer></script>
</head>

<body>
    <!-- Sidebar -->
    <aside id="sidebar">
        <nav><a href="dashboard.php">Dashboard</a></nav>
        <nav class="active">Incidents</nav>
        <nav><a href="clients.php">Clients</a></nav>
        <!-- <?php if ($_SESSION["role"] == "admin"): ?>
            <nav><a href="users.php">Users</a></nav>
        <?php endif; ?> -->
    </aside>
    
    <!-- Main Content -->
    <div id="main-content">
        <header id="incidents-header">
            <h1>Incidents</h1>
            <nav>
                <a id="new-incident-button" href="newincident.php">+ New Incident</a>
            </nav>
        </header>

        <main id="incidents-list-container">
            <section>
                <table class="data-table" id="incidents-table">
                    <thead>
                        <tr>
                            <th>
                                ID
                                <button class="sort-btn" data-col="0">⇅</button>
                                <input type="text" class="search-input" placeholder="Search ID">
                            </th>
                            <th>
                                Title
                                <button class="sort-btn" data-col="1">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Title">
                            </th>
                            <th>
                                Client
                                <button class="sort-btn" data-col="2">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Client">
                            </th>
                            <th>
                                Assigned User
                                <button class="sort-btn" data-col="3">⇅</button>
                                <input type="text" class="search-input" placeholder="Search User">
                            </th>
                            <th>
                                Severity
                                <button class="sort-btn" data-col="4">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Severity">
                            </th>
                            <th>
                                Status
                                <button class="sort-btn" data-col="5">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Status">
                            </th>
                            <th>
                                Date Created
                                <button class="sort-btn" data-col="6">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Date">
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($incidents)):?>
                            <?php foreach($incidents as $incident):?>
                            <tr>
                                <td><?= "#" . str_pad($incident['incidentID'], 4, "0", STR_PAD_LEFT) ?></td>
                                <td><?= htmlspecialchars($incident['title']) ?></td>
                                <td><?= htmlspecialchars($incident['Client']) ?></td>
                                <td><?= htmlspecialchars($incident['Assigned User']) ?></td>
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
                                    <td><a href="#">View</a> | <a href="#">Edit</a></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>
</html>
