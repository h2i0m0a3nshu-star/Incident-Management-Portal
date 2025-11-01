<?php
require_once("db.php");
$users;

// Database connection
try {
    $conn = new mysqli($db_host, $db_user, $db_pwd, $db_db);
    if ($conn->connect_error) {
        throw new mysqli_sql_exception($conn->connect_error, $conn->connect_errno);
    }
} catch (mysqli_sql_exception $e) {
    $error["Database Connection"] = "Could not connect to the database.";
}

$query = "
    SELECT userID, firstName, lastName, email, status
    FROM users;
";
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt->get_result();
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en-US">

<head>
    <meta charset="utf-8" />
    <title>Users</title>
    <link rel="stylesheet" href="css/styles.css" />
    <script src="js/eventhandler.js" defer></script>
</head>

<body>
   <!-- Sidebar -->
    <aside id="sidebar">
        <nav><a href="dashboard.php">Dashboard</a></nav>
        <nav><a href="incidents.php">Incidents</a></nav>
        <nav><a href="clients.php">Clients</a></nav>
        <nav class="active"><a href="users.php">Users</a></nav>
    </aside>

    <!-- Main Content -->
    <div id="main-content">
        <header id="clients-header">
            <h1>Users</h1>
        </header>

        <main id="clients-list-container">
            <section>
                <table class="data-table" id="clients-table">
                    <thead>
                        <tr>
                            <th>
                                ID
                                <button class="sort-btn" data-col="0">⇅</button>
                                <input type="text" class="search-input" placeholder="Search ID">
                            </th>
                            <th>
                                First Name
                                <button class="sort-btn" data-col="1">⇅</button>
                                <input type="text" class="search-input" placeholder="Search First">
                            </th>
                            <th>
                                Last Name
                                <button class="sort-btn" data-col="2">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Last">
                            </th>
                            <th>
                                Email
                                <button class="sort-btn" data-col="3">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Email">
                            </th>
                            <th>
                                Status
                                <button class="sort-btn" data-col="5">⇅</button>
                                <input type="text" class="search-input" placeholder="Search Status">
                            </th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($users)): ?>
                            <?php foreach($users as $user): ?>
                                <tr>
                                    <td><?= "#".str_pad($user['userID'], 4, "0", STR_PAD_LEFT) ?></td>
                                    <td><?= htmlspecialchars($user['firstName']) ?></td>
                                    <td><?= htmlspecialchars($user['lastName']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <?php if($user['status']): ?>
                                    <td><span class="tag active">Active</span></td>
                                    <?php endif ?>
                                    <?php if(!$user['status']): ?>
                                    <td><span class="tag inactive">Inactive</span></td>
                                    <?php endif ?>
                                </tr>
                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>
</body>

</html>
