<?php
// Database connection
$servername = "localhost";
$username = "id21575284_nomimusic";
$password = "Noman@123";
$dbname = "id21575284_nomimusic_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create User
if (isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $password = md5($_POST['password']); // Note: This is a basic example; use proper hashing in a real application

    $sql = "INSERT INTO users (username, firstName, lastName, email, password, signUpDate, profilePic) 
            VALUES ('$username', '$firstName', '$lastName', '$email', '$password', NOW(), 'assets/images/profile-pics/head_emerald.png')";

    if ($conn->query($sql) === TRUE) {
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Delete User
if (isset($_GET['delete_user'])) {
    $user_id = $_GET['delete_user'];

    $sql = "DELETE FROM users WHERE id=$user_id";

    if ($conn->query($sql) === TRUE) {
        header("Location: $_SERVER[PHP_SELF]");
        exit();
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}

// Read Users
$sql = "SELECT * FROM users";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
</head>
<body>
<h1><a href="admin/admin.html">Admin Home</a></h1>
<!-- Create User Form -->
<h2>Create User</h2>
<form action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" required>
    <br>
    <label for="firstName">First Name:</label>
    <input type="text" name="firstName" required>
    <br>
    <label for="lastName">Last Name:</label>
    <input type="text" name="lastName" required>
    <br>
    <label for="email">Email:</label>
    <input type="email" name="email" required>
    <br>
    <label for="password">Password:</label>
    <input type="password" name="password" required>
    <br>
    <input type="submit" name="create_user" value="Create User">
</form>

<!-- Display Users -->
<h2>Users</h2>
<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Email</th>
        <th>Sign-Up Date</th>
        <th>Action</th>
    </tr>
    <?php
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['firstName']}</td>
                    <td>{$row['lastName']}</td>
                    <td>{$row['email']}</td>
                    <td>{$row['signUpDate']}</td>
                    <td>
                        <a href=\"$_SERVER[PHP_SELF]?delete_user={$row['id']}\">Delete</a>
                    </td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='7'>No users found</td></tr>";
    }
    ?>
</table>

</body>
</html>

<?php
// Close database connection
$conn->close();
?>
