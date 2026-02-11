<?php
session_start();

// Prevent caching so browser back button won't show a cached login page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

$email = "";
$password = "";

$errorMessage = "";
$successMessage = "";

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: /myshop/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST["email"];
    $password = $_POST["password"];

    do {
        if (empty($email) || empty($password)) {
            $errorMessage = "Email and password are required";
            break;
        }

        // Establish DB connection and verify user
        $servername = "localhost";
        $username = "root";
        $password_db = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password_db, $database);
        if ($connection->connect_error) {
            $errorMessage = "Connection Failed: " . $connection->connect_error;
            break;
        }

        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $connection->error;
            $connection->close();
            break;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $row["password"])) {
                // Set session variables
                $_SESSION['user_id'] = $row["id"];
                $_SESSION['user_name'] = $row["name"];
                $_SESSION['user_email'] = $row["email"];
                
                $stmt->close();
                $connection->close();

                // Redirect to index page after successful login
                header("Location: /myshop/index.php");
                exit;
            } else {
                $errorMessage = "Invalid email or password";
            }
        } else {
            $errorMessage = "Invalid email or password";
        }

        $stmt->close();
        $connection->close();

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2>Login</h2>

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

            <form method="post">
                <!-- Email -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Email</label>
                    <div class="col-sm-9">
                        <input type="email" class="form-control" name="email" value="<?php echo $email; ?>">
                    </div>
                </div>

                <!-- Password -->
                <div class="row mb-3">
                    <label class="col-sm-3 col-form-label">Password</label>
                    <div class="col-sm-9">
                        <input type="password" class="form-control" name="password">
                    </div>
                </div>

                <!-- Buttons -->
                <div class="row mb-3">
                    <div class="offset-sm-3 col-sm-3 d-grid">
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                </div>
            </form>

            <p class="mt-3">Don't have an account? <a href="/myshop/register.php">Register here</a></p>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
