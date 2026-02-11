<?php
$name = "";
$email = "";
$phone = "";
$address = "";
$id = "";

$errorMessage = "";
$successMessage = "";

// Get the id from URL parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Connect and fetch the existing client data
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "myshop";

    $connection = new mysqli($servername, $username, $password, $database);
    if ($connection->connect_error) {
        die("Connection Failed: " . $connection->connect_error);
    }

    $sql = "SELECT * FROM clients WHERE id = ?";
    $stmt = $connection->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $connection->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row["name"];
        $email = $row["email"];
        $phone = $row["phone"];
        $address = $row["address"];
    } else {
        $errorMessage = "Client not found";
    }

    $stmt->close();
    $connection->close();
} else {
    $errorMessage = "No client ID provided";
}

// Handle POST submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    $id = $_POST["id"];

    do {
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            $errorMessage = "All the fields are required";
            break;
        }

        // Establish DB connection and update the client
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password, $database);
        if ($connection->connect_error) {
            $errorMessage = "Connection Failed: " . $connection->connect_error;
            break;
        }

        $sql = "UPDATE clients SET name=?, email=?, phone=?, address=? WHERE id=?";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $connection->error;
            $connection->close();
            break;
        }

        $stmt->bind_param("ssssi", $name, $email, $phone, $address, $id);
        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Check for duplicate entry error (MySQL error 1062)
            if ($e->getCode() === 1062) {
                $errorMessage = "This email already exists. Please use a different email.";
            } else {
                $errorMessage = "Update failed: " . $e->getMessage();
            }
            $stmt->close();
            $connection->close();
            break;
        }

        $stmt->close();
        $connection->close();

        $successMessage = "Client updated correctly";

        // Redirect to index page after successful update
        header("Location: /myshop/index.php");
        exit;

    } while (false);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <h2>Edit Client</h2>

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
        <input type="hidden" name="id" value="<?php echo $id; ?>">

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
                <input type="text" class="form-control" name="email" value="<?php echo $email; ?>">
            </div>
        </div>

        <!-- Phone -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Phone</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="phone" value="<?php echo $phone; ?>">
            </div>
        </div>

        <!-- Address -->
        <div class="row mb-3">
            <label class="col-sm-3 col-form-label">Address</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="address" value="<?php echo $address; ?>">
            </div>
        </div>

        <!-- Buttons -->
        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            <div class="col-sm-3 d-grid">
                <a class="btn btn-outline-primary" href="/myshop/index.php" role="button">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
