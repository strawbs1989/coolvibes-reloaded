<?php
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
        $DATABASE_HOST="aws.connect.psdb.cloud";
        $DATABASE_USERNAME="pv12v5sekt6fudyk0762";
        $DATABASE_PASSWORD="laurajay1998";
        $dbname = "coolvibes-reloaded";

        // Create connection
        $conn = new mysqli($servername, $db_username, $db_password, $dbname);

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

        // Generate a unique user ID
        $userid = generateUniqueUserId();

        // Prepare and bind the SQL statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO registration (userid, signup_date, yourname, lastname, username, email, password) VALUES (?, CURRENT_TIMESTAMP(), ?, ?, ?, ?, ?)");

        // Check if the prepare statement was successful
        if ($stmt === false) {
            die("Prepare failed: " . $conn->error);
        }

        // Bind the parameters
        $stmt->bind_param("ssssss", $userid, $yourname, $lastname, $username, $email, $hashedPassword);

        // Execute the prepared statement
        if ($stmt->execute()) {
            // Send email notification using PHPMailer
            require 'PHPMailer/src/PHPMailer.php';
            require 'PHPMailer/src/SMTP.php';

            $to = $email;
            $subject = "Registration Successful";
            $message = "Thank you for registering! We're happy to have you on board!";
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

            // Create an HTML message with "Registration Successful" and an image
            $message = "<html><body>";
            $message .= "<p><img src='https://coolvibes-reloaded.com/img/favicon.png' alt='' width='708' height='142'></p>";
            $message .= "<h1>Registration Successful</h1>";
            $message .= "<p>Welcome to our website. We're excited to have you as a member.</p>";
            $message .= "</body></html>";
            $mail->msgHTML($message);

            if ($mail->send()) {
                echo 'Registration successful!';
            } else {
                echo 'Email sending failed. Check your email configuration.';
                echo 'Error: ' . $mail->ErrorInfo;
            }

            // Close the connection
            $stmt->close();
            $conn->close();
        } else {
            echo 'Registration failed. Please try again later.';
        }
    }
}
?>
