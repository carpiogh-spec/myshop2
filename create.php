<?php
$name = "";
$email = "";
$phone = "";
$address = "";

$errorMessage ="";
$successMessage ="";
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $address = $_POST["address"];
    do{
        if(empty ($name) || empty($email) || empty ($phone) || empty ($address)){
            $errorMessage = "All the fields are required";
            break;
        }

        // Establish DB connection and insert the new client
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "myshop";

        $connection = new mysqli($servername, $username, $password, $database);
        if ($connection->connect_error) {
            $errorMessage = "Connection Failed: " . $connection->connect_error;
            break;
        }

        $sql = "INSERT INTO clients (name, email, phone, address) VALUES (?, ?, ?, ?)";
        $stmt = $connection->prepare($sql);
        if (!$stmt) {
            $errorMessage = "Prepare failed: " . $connection->error;
            $connection->close();
            break;
        }

        $stmt->bind_param("ssss", $name, $email, $phone, $address);
        try {
            $stmt->execute();
        } catch (mysqli_sql_exception $e) {
            // Check for duplicate entry error (MySQL error 1062)
            if ($e->getCode() === 1062) {
                $errorMessage = "This email already exists. Please use a different email.";
            } else {
                $errorMessage = "Insert failed: " . $e->getMessage();
            }
            $stmt->close();
            $connection->close();
            break;
        }

        $stmt->close();
        $connection->close();

        // Clear form fields only after successful insert
        $name = "";
        $email = "";
        $phone = "";
        $address = "";
        $successMessage = "Client added correctly";

        // Redirect to index page after successful insert
        header("Location: /myshop/index.php");
        exit;

    }while(false);
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
    <h2>New Client</h2>

    <?php
    if (!empty ($errorMessage)){

    }
    ?>

    <?php
    if (!empty($errorMessage)){
        echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
            <strong>$errorMessage</strong>
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

        <?php
        if(!empty($successMessage)){
            echo "
            <div class='alert alert-success alert-dismissible fade show' role='alert'>
                <strong>$successMessage</strong>
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
            </div>
            ";
        }
        ?>

        <!-- Buttons -->
        <div class="row mb-3">
            <div class="offset-sm-3 col-sm-3 d-grid">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            <div class="col-sm-3 d-grid">
                <a class="btn btn-outline-primary" href="/myshop/index.php" role="button">Cancel</a>
            </div>
        </div>
    </form>
</div>

</body>
</html>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
