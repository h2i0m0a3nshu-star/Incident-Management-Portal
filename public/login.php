<?php
session_start();
require_once("db.php");
  
function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

if($_SERVER["REQUEST_METHOD"] == "POST"){
  $email = test_input($_POST["email"]);
  $password = test_input($_POST["password"]);
  $flag = FALSE;
  $error = array();

  $emailRegex = '/^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}$/i';
  $passwordRegex = '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*()\-_=+{};:,<.>]).{8,}$/';

  if(!preg_match($emailRegex, $email)){
    $error["email"] = "Invalid Email Address";
    $flag = TRUE;
  }
  if(!preg_match($passwordRegex, $password)){
    $error["password"] = "Invalid Password";
    $flag = TRUE;
  }

  if(!$flag){
  
    try {
      $conn = new mysqli($db_host, $db_user, $db_pwd, $db_db);
      if ($conn->connect_error) {
        throw new mysqli_sql_exception($conn->connect_error, $conn->connect_errno);
      }
    } catch (mysqli_sql_exception $e) {
        $error["Database Connection"] = "Could not connect to the database.";
        $flag = TRUE;
    }

    $query = "SELECT * FROM users WHERE email= ? AND password= ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result) {
      $error["Database Error"] = "Could not retrieve user information";
    }elseif ($row = $result->fetch_assoc()) {
      $_SESSION["userID"]    = $row["userID"];
      $_SESSION["email"]     = $email;
      $_SESSION["firstName"] = $row["fistName"];
      $_SESSION["lastName"] = $row["lastName"]; 
      $_SESSION["role"] = $row["role"]; 
      $_SESSION["status"] = $row["status"]; 
      
      $conn->close();

      header("Location: dashboard.php");
      exit();
    } else {
      $error["Login Failed"] = "That username/password combination does not exist.";
    }

  }else{
    $error["login failed"] = "You have entered an Invalid set of credentials.";
  }
}

?>

<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | Incident Management Portal</title>
  <link rel="stylesheet" href="css/styles.css" />
  <script src="js/eventhandler.js"></script>
  <?php if (!empty($error)): ?>
    <div class="alert" id="error-alert">
        <?php foreach ($error as $type => $message): ?>
            <p><strong><?php echo htmlspecialchars($type); ?>:</strong> <?php echo htmlspecialchars($message); ?></p>
        <?php endforeach; ?>
    </div>
  <?php endif; ?>
</head>

<body>
  <div id="login-form-container">
    <section class="box-container" aria-labelledby="login-heading">

      <header>
        <h1 id="login-heading">Incident Management Portal</h1>
      </header>

      <main>
        <p class="login-subtitle">
          Secure access to manage and track incidents
        </p>
        <form id="login-form" method="post" action="login.php">
          <fieldset class="no-border">
            <div class="form-group">
              <label for="email" class="visually-hidden">Email</label>
              <input id="email" name="email" type="email" placeholder="Email" required />
            </div>

            <div class="form-group">
              <label for="password" class="visually-hidden">Password</label>
              <input id="password" name="password" type="password" placeholder="Password" required />
            </div>
            <nav>
              <a href="forgotpassword.php">
                Forgot Password ?
              </a>
            </nav>
            <button type="submit" id="login-button">Login</button>
          </fieldset>
        </form>
        <p>
          New here ? Please contact an adminstrator to create an account for you.
        </p>
      </main>

    </section>
  </div>
  <script src="js/loginformevent.js"></script>
</body>

</html>