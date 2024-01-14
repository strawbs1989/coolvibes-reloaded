<?php
require 'phpmailer/vendor/autoload.php';

$errors = [];
$errorMessage = '';
$successMessage = '';
$siteKey = ''; // reCAPTCHA site key
$secret = ''; // reCAPTCHA secret key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $message = sanitizeInput($_POST['message']);
    $recaptchaResponse = sanitizeInput($_POST['g-recaptcha-response']);

  $recaptchaUrl = "https://www.google.com/recaptcha/api/siteverify?secret={$secret}&response={$recaptchaResponse}";
  $verify = json_decode(file_get_contents($recaptchaUrl));
  
  if (!$verify->success) {
    $errors[] = 'Recaptcha failed';
  }
  if (empty($name)) {
    $errors[] = 'Name is empty';
  }
  if (empty($email)) {
    $errors[] = 'Email is empty';
  }  else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Email is invalid';
  }
  if (empty($message)) {
    $errors[] = 'Message is empty';
  }

  if (!empty($errors)) {
    $allErrors = join('<br/>', $errors);
    $errorMessage = "<p style='color: red;'>{$allErrors}</p>";
  } else {
    $toEmail = 'jay@requests.cu.ma';
    $emailSubject = 'New email from your contaсt form';

      // Create a new PHPMailer instance
        $mail = new PHPMailer(true);
        try {
            // Configure the PHPMailer instance
            $mail->isSMTP();
            $mail->Host = 'mail.requests.cu.ma';
            $mail->SMTPAuth = true;
            $mail->Username = 'jay@requests.cu.ma';
            $mail->Password = 'Laurajay223';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Set the sender, recipient, subject, and body of the message
            $mail->setFrom($email);
            $mail->addAddress($toEmail);
            $mail->Subject = $emailSubject;
            $mail->isHTML(true);
            $mail->Body = "<p>Name: {$name}</p><p>Email: {$email}</p><p>Message: {$message}</p>";

            // Send the message
            $mail->send();

            $successMessage = "<p style='color: green;'>Thank you for contacting us :)</p>";
        } catch (Exception $e) {
      $errorMessage = "<p style='color: red;'>Oops, something went wrong. Please try again later</p>";
    }
  }
}

function sanitizeInput($input) {
   $input = trim($input);
   $input = stripslashes($input);
   $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
   return $input;
}

?>

<html>
  <body>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <form action="http://requests.cu.ma/aubrey/oldtest/send.php"method="post" id="contact-form">
      <h2>Contact us</h2>
      <?php echo((!empty($errorMessage)) ? $errorMessage : '') ?>
      <?php echo((!empty($successMessage)) ? $successMessage : '') ?>
      <p>
        <label>First Name:</label>
        <input name="name" type="text" required />
      </p>
      <p>
        <label>Email Address:</label>
        <input style="cursor: pointer;" name="email" type="email" required />
      </p>
      <p>
        <label>Message:</label>
        <textarea name="message" required></textarea>
      </p>
      <p>
        <button
        class="g-recaptcha"
        type="submit"
        data-sitekey="<?php echo $siteKey ?>"
        data-callback='onRecaptchaSuccess'
        >
          Submit
        </button>
      </p>
    </form>

    <script>
    function onRecaptchaSuccess() {
      document.getElementById('contact-form').submit();
    }
    </script>
  </body>
</html>