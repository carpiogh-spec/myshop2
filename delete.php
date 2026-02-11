<?php
$id = "";
$errorMessage = "";
$successMessage = "";

// Get the id from URL parameter
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Establish DB connection and delete the client
    $servername = "localhost";
    $username = "root";
    $password = "";
    $database = "myshop";

    $connection = new mysqli($servername, $username, $password, $database);
    if ($connection->connect_error) {
        $errorMessage = "Connection Failed: " . $connection->connect_error;
    } else {
        $sql = "DELETE FROM clients WHERE id = ?";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $connection->error;
        } else {
            $stmt->bind_param("i", $id);
            try {
                $stmt->execute();
                $successMessage = "Client deleted successfully";
                $stmt->close();
                $connection->close();
                // Redirect to index page after successful delete
                header("Location: /myshop/index.php");
                exit;
            } catch (mysqli_sql_exception $e) {
                $errorMessage = "Delete failed: " . $e->getMessage();
                $stmt->close();
                $connection->close();
            }
        }
    }
} else {
    $errorMessage = "No client ID provided";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Client - My Shop</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css">
</head>
<body>

<div class="container my-5">
    <h2>Delete Client</h2>

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

    <a class="btn btn-secondary" href="/myshop/index.php">Back to List</a>
</div>

</body>
</html>