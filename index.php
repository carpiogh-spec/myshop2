<?php
session_start();

// Prevent caching so browser back button won't show a cached login page
header("Cache-Control: no-cache, no-store, must-revalidate"); // HTTP 1.1.
header("Pragma: no-cache"); // HTTP 1.0.
header("Expires: 0"); // Proxies.

// Redirect to login if user is not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: /myshop/login.php");
    exit;
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
    <h2>List of Clients</h2>
    <div class="d-flex justify-content-between mb-3">
        <a class="btn btn-primary" href="/myshop/create.php">New Client</a>
        <a class="btn btn-danger" href="/myshop/logout.php">Logout</a>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Address</th>
                <th>Created At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password, $database);

        if ($connection->connect_error) {
            die("Connection Failed: " . $connection->connect_error);
        }

        $sql = "SELECT * FROM clients";
        $result = $connection->query($sql);

        if (!$result) {
            die("Invalid query: " . $connection->error);
        }

        while ($row = $result->fetch_assoc()) {
            echo "
            <tr>
                <td>{$row['id']}</td>
                <td>{$row['name']}</td>
                <td>{$row['email']}</td>
                <td>{$row['phone']}</td>
                <td>{$row['address']}</td>
                <td>{$row['created_at']}</td>
                <td>
                    <a class='btn btn-primary btn-sm' href='/myshop/edit.php?id={$row['id']}'>Edit</a>
                    <a class='btn btn-danger btn-sm' href='/myshop/delete.php?id={$row['id']}'>Delete</a>
                </td>
            </tr>
            ";
        }
        ?>

        </tbody>
    </table>
</div>

</body>
</html>
