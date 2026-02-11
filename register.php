<?php
session_start();

$name = "";
$email = "";
$password = "";
$confirm_password = "";

$errorMessage = "";
$successMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    do {
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            $errorMessage = "All the fields are required";
            break;
        }

        if ($password !== $confirm_password) {
            $errorMessage = "Passwords do not match";
            break;
        }

        if (strlen($password) < 6) {
            $errorMessage = "Password must be at least 6 characters";
            break;
        }

        // Establish DB connection and register new user
        $servername = "localhost";
        $username = "root";
        $password_db = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password_db, $database);
        if ($connection->connect_error) {
            $errorMessage = "Connection Failed: " . $connection->connect_error;
            break;
        }

        // Check if email already exists
        $sql_check = "SELECT id FROM users WHERE email = ?";
        $stmt_check = $connection->prepare($sql_check);
        if (!$stmt_check) {
            $errorMessage = "Prepare failed: " . $connection->error;
            $connection->close();
            break;
        }

        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            $errorMessage = "This email is already registered. Please use a different email.";
            $stmt_check->close();
            $connection->close();
            break;
        }

        $stmt_check->close();

        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $connection->error;
            $connection->close();
            break;
        }

        $stmt->bind_param("sss", $name, $email, $hashed_password);
        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            $errorMessage = "Registration failed: " . $e->getMessage();
            $stmt->close();
            $connection->close();
            break;
        }

        $stmt->close();
        $connection->close();

        // Clear form fields only after successful registration
        $name = "";
        $email = "";
        $password = "";
        $confirm_password = "";
        $successMessage = "Registration successful! You can now login.";

        // Ensure the user must login again after registering
        session_unset();
        session_destroy();

        // Redirect to login page after successful registration
        header("Location: /myshop/login.php");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <h2>Register</h2>

    <?php
    if (!empty($errorMessage)) {
        echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>$errorMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    ?>

    <?php
    if (!empty($successMessage)) {
        echo "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
            <strong>$successMessage</strong>
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    ?>

    <form method="post">
        <!-- Name -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Name</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" value="<?php echo $name; ?>">
            </div>
        </div>

        <!-- Email -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Email</label>
            <div class="col-sm-6">
                <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
            </div>
        </div>

        <!-- Password -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Password</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="password">
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Confirm Password</label>
            <div class="col-sm-6">
                <input type="password" class="form-control" name="confirm_password">
            </div>
        </div>

        <!-- Buttons -->
        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Register</button>
            </div>
            <div class="col-sm-3 d-grid">
                <a class="btn btn-outline-primary" href="/myshop/login.php" role="button">Back to Login</a>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
