    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $selectedSong = $_POST["song"];
    $name = $_POST["name"];
    $message = $_POST["message"];

    // Database connection settings
    $servername = "sql311.byethost8.com";
    $username = "b8_34833020";
    $password = "Laurajay1998";
    $dbname = "b8_34833020_coolvibes";

    // Create a connection to the database
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check if the connection was successful
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare and execute an SQL query to insert data
    $sql = "INSERT INTO song_requests (SelectedSong, Name, Message) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $selectedSong, $name, $message);
    
    if ($stmt->execute()) {
        echo "Request submitted successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    // Close the database connection
    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>

    
    // Return a response (e.g., success or error message)
    echo "Request submitted successfully!";
} else {
    echo "Invalid request.";
}
?>
