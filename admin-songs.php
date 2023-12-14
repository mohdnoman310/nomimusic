<?php
// Database Connection
$servername = "localhost";
$username = "id21575284_nomimusic";
$password = "Noman@123";
$dbname = "id21575284_nomimusic_db";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Function to fetch data from related tables
function getRelatedData($table, $id) {
    global $conn;
    $result = mysqli_query($conn, "SELECT * FROM $table WHERE id = $id");
    return ($result) ? mysqli_fetch_assoc($result) : false;
}

// Create a new song
if(isset($_POST['submit'])) {
    $title = $_POST['title'];
    $artistId = $_POST['artist'];
    $albumId = $_POST['album'];
    $genreId = $_POST['genre'];
    $duration = $_POST['duration'];

    // Assuming you have a folder named 'uploads' to store songs
    $target_dir = "assets/music/";
    $target_file = $target_dir . basename($_FILES["song"]["name"]);

    if (move_uploaded_file($_FILES["song"]["tmp_name"], $target_file)) {
        // Insert data into Songs table
        $sql = "INSERT INTO Songs (title, artist, album, genre, duration, path) 
                VALUES ('$title', $artistId, $albumId, $genreId, '$duration', '$target_file')";
        
        if(mysqli_query($conn, $sql)) {
            echo "Song uploaded successfully!";
        } else {
            echo "Error: " . $sql . "<br>" . mysqli_error($conn);
        }
    } else {
        echo "Error uploading file.";
    }
}

// Fetch all songs
$result = mysqli_query($conn, "SELECT * FROM Songs");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Song Management</title>
</head>
<body>
    <h2>Song Management</h2>
    <h1><a href="admin/admin.html">Admin Home</a></h1>

    <!-- Form to upload a new song -->
    <form action="" method="post" enctype="multipart/form-data">
        <label for="title">Song Title:</label>
        <input type="text" name="title" required><br>

        <label for="artist">Artist:</label>
        <select name="artist" required>
            <?php
                $artists = mysqli_query($conn, "SELECT * FROM artists");
                while($artist = mysqli_fetch_assoc($artists)) {
                    echo "<option value='{$artist['id']}'>{$artist['name']}</option>";
                }
            ?>
        </select><br>

        <label for="album">Album:</label>
        <select name="album" required>
            <?php
                $albums = mysqli_query($conn, "SELECT * FROM albums");
                while($album = mysqli_fetch_assoc($albums)) {
                    echo "<option value='{$album['id']}'>{$album['title']}</option>";
                }
            ?>
        </select><br>

        <label for="genre">Genre:</label>
        <select name="genre" required>
            <?php
                $genres = mysqli_query($conn, "SELECT * FROM genres");
                while($genre = mysqli_fetch_assoc($genres)) {
                    echo "<option value='{$genre['id']}'>{$genre['name']}</option>";
                }
            ?>
        </select><br>

        <label for="duration">Duration:</label>
        <input type="text" name="duration" required><br>

        <label for="song">Upload Song:</label>
        <input type="file" name="song" accept=".mp3" required><br>

        <input type="submit" name="submit" value="Upload Song">
    </form>

    <!-- Display all songs -->
    <h3>All Songs</h3>
    <ul>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            $artist = getRelatedData('artists', $row['artist']);
            $album = getRelatedData('albums', $row['album']);
            $genre = getRelatedData('genres', $row['genre']);

            echo "<li>{$row['title']} by {$artist['name']} from {$album['title']} ({$genre['name']}) - Duration: {$row['duration']}</li>";
        }
        ?>
    </ul>
</body>
</html>

<?php
// Close the connection
mysqli_close($conn);
?>
