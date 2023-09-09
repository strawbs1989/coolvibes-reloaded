<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $yourname = filter_input(INPUT_POST, "yourname", FILTER_SANITIZE_STRING);
    $lastname = filter_input(INPUT_POST, "lastname", FILTER_SANITIZE_STRING);
    $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
    $password = $_POST["password"];

    // Validate inputs
    if (empty($yourname) || empty($lastname) || empty($username) || empty($email) || empty($password)) {
        echo "Please fill in all fields.";
    } else {
        // Hash the password (ensure you have proper hashing and salting)
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Database connection settings
        $servername = "sql311.byethost8.com"; // Replace with your actual server name
        $username = "b8_34833020"; // Replace with your actual database username
        $password = "Laurajay1998"; // Replace with your actual database password
        $dbname = "b8_34833020_coolvibes"; // Replace with your actual database name


        // Create connection
        $conn = new mysqli($servername, $connection_username, $connection_password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Function to generate a unique user ID
        function generateUniqueUserId() {
            $timestamp = time(); // Get the current timestamp
            $randomNumber = mt_rand(1000, 9999); // Generate a random 4-digit number

            // Combine the timestamp and random number to create a unique ID
            $userid = "USR" . $timestamp . $randomNumber;

            return $userid;
        }

        // Set the timezone to GMT+1 (Central European Time)
        date_default_timezone_set("Europe/London");

        // Prepare and bind the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO registration (yourname, lastname, username, email, password, signup_date) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP())");

        // Check if the prepare statement was successful
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind the parameters
        $bind_result = $stmt->bind_param("sssss", $yourname, $lastname, $username, $email, $hashedPassword);

        // Check if the binding was successful
        if ($bind_result === false) {
            die("Binding parameters failed: " . $stmt->error);
        }

        // Send email notification using PHPMailer
        require 'PHPMailer/PHPMailer.php';
        require 'PHPMailer/SMTP.php';

        $to = $email;
        $subject = "Registration Successful";
        $message = "Thank you for registering!";
        $headers = "From: coolvibes1989@gmail.com"; // Replace with your email address or sender address

        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'jayaubs89@gmail.com'; // Replace with your Gmail email
        $mail->Password = 'eawdimrjlashvwrp'; // Use an App Password if 2FA is enabled
        $mail->SMTPSecure = 'tls'; // Use 'ssl' if you prefer SSL
        $mail->Port = 587;
        $mail->setFrom('coolvibes1989@gmail.com', 'CoolVibes-Reloaded'); // Replace with your name and email
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

        if ($mail->send()) {
            echo 'Registration successful!';
        } else {
            echo 'Email sending failed. Check your email configuration.';
            echo 'Error: ' . $mail->ErrorInfo;
        }

        // Close the connection
        $stmt->close();
        $conn->close();
    }
}
?>
