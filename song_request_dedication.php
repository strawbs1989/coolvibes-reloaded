<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the form data
    $selectedSong = $_POST["song"];
    $name = $_POST["name"];
    $message = $_POST["message"];

    // Perform any necessary processing (e.g., storing in a database, sending emails)
    
    // Return a response (e.g., success or error message)
    echo "Request submitted successfully!";
} else {
    echo "Invalid request.";
}
?>
