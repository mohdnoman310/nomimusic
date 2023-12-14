<?php
session_start(); // Start the session

// Database connection code
$servername = "localhost";
$username = "id21575284_nomimusic";
$password = "Noman@123";
$dbname = "id21575284_nomimusic_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create operation - Insert a new album
function createAlbum($conn, $title, $artist, $genre) {
    // Check if the directory exists, if not, create it
    $uploadDir = 'assets/images/artwork/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique filename based on album name, artist name, and timestamp
    $uniqueFilename = $artist . '_' . $title . '_' . time() . '.jpg'; // or any other desired extension
    $targetPath = $uploadDir . $uniqueFilename;

    // Move uploaded file to the destination directory
    if (move_uploaded_file($_FILES['artwork']['tmp_name'], $targetPath)) {
        // Insert album information into the database
        $sql = "INSERT INTO albums (title, artist, genre, artworkPath) VALUES ('$title', '$artist', '$genre', '$targetPath')";
        $conn->query($sql);
    }
}

// Delete operation - Delete an album
function deleteAlbum($conn, $albumId) {
    // Fetch the album details
    $result = $conn->query("SELECT * FROM albums WHERE id = $albumId");

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $artworkPath = $row['artworkPath'];

        // Delete the album from the database
        $sql = "DELETE FROM albums WHERE id = $albumId";
        if ($conn->query($sql)) {
            // Delete the image file from the folder
            if (file_exists($artworkPath)) {
                unlink($artworkPath);
            }
        }
    }
}

// Update operation - Edit album details
function updateAlbum($conn, $albumId, $title, $artist, $genre) {
    // Check if the directory exists, if not, create it
    $uploadDir = './assets/images/artwork/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Check if a new artwork file is uploaded
    if ($_FILES['artwork']['error'] == UPLOAD_ERR_OK) {
        // Generate a unique filename based on album name, artist name, and timestamp
        $uniqueFilename = $artist . '_' . $title . '_' . time() . '.jpg'; // or any other desired extension
        $targetPath = $uploadDir . $uniqueFilename;

        // Move uploaded file to the destination directory
        if (move_uploaded_file($_FILES['artwork']['tmp_name'], $targetPath)) {
            // Fetch the old artwork path
            $result = $conn->query("SELECT artworkPath FROM albums WHERE id = $albumId");
            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $oldArtworkPath = $row['artworkPath'];

                // Update album information in the database
                $sql = "UPDATE albums SET title = '$title', artist = '$artist', genre = '$genre', artworkPath = '$targetPath' WHERE id = $albumId";
                $conn->query($sql);

                // Delete the old image file from the folder
                if (file_exists($oldArtworkPath)) {
                    unlink($oldArtworkPath);
                }
            }
        }
    } else {
        // No new artwork file uploaded, update album information without changing the artwork
        $sql = "UPDATE albums SET title = '$title', artist = '$artist', genre = '$genre' WHERE id = $albumId";
        $conn->query($sql);
    }
}

// Check if the form has been submitted for create, update, or delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_SESSION['form_submitted'])) {
    // Create operation - Insert a new album
    if (isset($_POST['submit'])) {
        $title = $_POST['title'];
        $artist = $_POST['artist'];
        $genre = $_POST['genre'];

        createAlbum($conn, $title, $artist, $genre);

        // Set the session variable to indicate form submission
        $_SESSION['form_submitted'] = true;

        // Redirect to the same page to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Delete operation - Delete an album
    if (isset($_POST['delete'])) {
        $albumIdToDelete = $_POST['album_id_to_delete'];

        deleteAlbum($conn, $albumIdToDelete);

        // Set the session variable to indicate form submission
        $_SESSION['form_submitted'] = true;

        // Redirect to the same page to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Update operation - Edit album details
    if (isset($_POST['update'])) {
        $albumIdToUpdate = $_POST['album_id_to_update'];
        $title = $_POST['title'];
        $artist = $_POST['artist'];
        $genre = $_POST['genre'];

        updateAlbum($conn, $albumIdToUpdate, $title, $artist, $genre);

        // Set the session variable to indicate form submission
        $_SESSION['form_submitted'] = true;

        // Redirect to the same page to prevent form resubmission
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Album Management</title>
</head>
<body>
<h1><a href="admin/admin.html">Admin Home</a></h1>
    <!-- Form for creating album -->
    <form method="post" enctype="multipart/form-data">
        <h2>Create Album</h2>
        <label for="title">Title:</label>
        <input type="text" name="title" required>

        <!-- Dropdown for Artists -->
        <label for="artist">Artist:</label>
        <select name="artist" required>
            <?php
            // Populate dropdown with artists
            $artists = $conn->query("SELECT * FROM artists");
            while ($artist = $artists->fetch_assoc()) {
                echo "<option value='{$artist['id']}'>{$artist['name']}</option>";
            }
            ?>
        </select>

        <!-- Dropdown for Genres -->
        <label for="genre">Genre:</label>
        <select name="genre" required>
            <?php
            // Populate dropdown with genres
            $genres = $conn->query("SELECT * FROM genres");
            while ($genre = $genres->fetch_assoc()) {
                echo "<option value='{$genre['id']}'>{$genre['name']}</option>";
            }
            ?>
        </select>

        <label for="artwork">Artwork:</label>
        <input type="file" name="artwork" accept="image/*" required>

        <button type="submit" name="submit">Add Album</button>
    </form>

    <!-- Form for updating album -->
    <form method="post" enctype="multipart/form-data">
        <h2>Update Album</h2>

        <!-- Dropdown for selecting album to update -->
        <label for="album_id_to_update">Select Album to Update:</label>
        <select name="album_id_to_update" required>
            <?php
            // Fetch albums for dropdown
            $albums = $conn->query("SELECT * FROM albums");
            while ($album = $albums->fetch_assoc()) {
                $artistResult = $conn->query("SELECT name FROM artists WHERE id = {$album['artist']}");
                $artistName = ($artistResult->num_rows > 0) ? $artistResult->fetch_assoc()['name'] : "Unknown Artist";
                echo "<option value='{$album['id']}'>{$album['title']} by {$artistName}</option>";
            }
            ?>
        </select>

        <!-- Input fields for updating album details -->
        <label for="title">Title:</label>
        <input type="text" name="title" required>

        <label for="artist">Artist:</label>
        <select name="artist" required>
            <?php
            // Populate dropdown with artists
            $artists = $conn->query("SELECT * FROM artists");
            while ($artist = $artists->fetch_assoc()) {
                echo "<option value='{$artist['id']}'>{$artist['name']}</option>";
            }
            ?>
        </select>

        <label for="genre">Genre:</label>
        <select name="genre" required>
            <?php
            // Populate dropdown with genres
            $genres = $conn->query("SELECT * FROM genres");
            while ($genre = $genres->fetch_assoc()) {
                echo "<option value='{$genre['id']}'>{$genre['name']}</option>";
            }
            ?>
        </select>

        <label for="artwork">New Artwork (optional):</label>
        <input type="file" name="artwork" accept="image/*">

        <button type="submit" name="update">Update Album</button>
    </form>

    <!-- Form for deleting album -->
    <form method="post">
        <h2>Delete Album</h2>

        <!-- Dropdown for selecting album to delete -->
        <label for="album_id_to_delete">Select Album to Delete:</label>
        <select name="album_id_to_delete" required>
            <?php
            // Fetch albums for dropdown
            $albums = $conn->query("SELECT * FROM albums");
            while ($album = $albums->fetch_assoc()) {
                $artistResult = $conn->query("SELECT name FROM artists WHERE id = {$album['artist']}");
                $artistName = ($artistResult->num_rows > 0) ? $artistResult->fetch_assoc()['name'] : "Unknown Artist";
                echo "<option value='{$album['id']}'>{$album['title']} by {$artistName}</option>";
            }
            ?>
        </select>

        <button type="submit" name="delete">Delete Album</button>
    </form>

    <?php
    // Close database connection
    $conn->close();
    ?>
</body>
</html>
