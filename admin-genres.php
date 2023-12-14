<?php
$servername = "localhost";
$username = "id21575284_nomimusic";
$password = "Noman@123";
$dbname = "id21575284_nomimusic_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Variable to store the action message
$actionMessage = '';

// Adding a new genre
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_genre'])) {
    $newGenreName = $_POST['new_genre_name'];

    $sql = "INSERT INTO genres (name) VALUES ('$newGenreName')";

    if ($conn->query($sql) === TRUE) {
        $actionMessage = "New genre added successfully";
    } else {
        $actionMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch existing genre names for dropdown (for update and delete)
$sql = "SELECT id, name FROM genres";
$result = $conn->query($sql);

// Updating an existing genre name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_genre'])) {
    $selectedGenreId = $_POST['selected_genre'];
    $updatedGenreName = $_POST['updated_genre_name'];

    $sql = "UPDATE genres SET name='$updatedGenreName' WHERE id='$selectedGenreId'";

    if ($conn->query($sql) === TRUE) {
        $actionMessage = "Genre name updated successfully";
    } else {
        $actionMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Deleting a genre name
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_genre'])) {
    $selectedGenreId = $_POST['selected_genre'];

    $sql = "DELETE FROM genres WHERE id='$selectedGenreId'";

    if ($conn->query($sql) === TRUE) {
        $actionMessage = "Genre deleted successfully";
    } else {
        $actionMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Genre Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        h1 {
            color: #333;
        }

        .container {
            display: flex;
            justify-content: space-around;
        }

        .crud-section,
        .table-section {
            width: 45%;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-sizing: border-box;
            margin: 10px;
            background-color: #f9f9f9;
        }

        .crud-section form {
            text-align: left;
            margin: 10px 0;
        }

        .crud-section form label {
            display: block;
            margin-bottom: 5px;
        }

        .crud-section form input,
        .crud-section form select,
        .crud-section form button {
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <h1>Genre Management</h1>
    <h1><a href="admin/admin.html">Admin Home</a></h1>

    <div class="container">
        <div class="crud-section">
            <h2>Add New Genre</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="new_genre_name">New Genre Name:</label>
                <input type="text" name="new_genre_name" required>
                <button type="submit" name="add_genre">Add Genre</button>
            </form>

            <hr>

            <h2>Update Existing Genre Name</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="selected_genre">Select Genre:</label>
                <select name="selected_genre">
                    <option value="">Select a genre</option>
                    <?php
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
                <br>
                <input type="text" name="updated_genre_name" required>
                <button type="submit" name="update_genre">Update Genre</button>
            </form>

            <hr>

            <h2>Delete Genre</h2>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="selected_genre">Select Genre:</label>
                <select name="selected_genre">
                    <option value="">Select a genre</option>
                    <?php
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
                    }
                    ?>
                </select>
                <button type="submit" name="delete_genre">Delete Genre</button>
            </form>

            <p><?php echo $actionMessage; ?></p>
        </div>

        <div class="table-section">
            <h2>Genre List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result->data_seek(0);
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['name'] . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
