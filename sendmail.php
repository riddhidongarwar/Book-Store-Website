<?php
include 'config.php';
session_start();
$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// require 'vendor/autoload.php';

//  Get values using $_GET
if (isset($_GET['name'], $_GET['email'], $_GET['number'], $_GET['message'])) {
    $name = $_GET['name'];
    $email = $_GET['email'];
    $number = $_GET['number'];
    $message = $_GET['message'];

    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    try {
        // Server settings
        //$mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->Debugoutput = 'html';
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'ridhswork17@gmail.com';
        $mail->Password   = 'ofapatxrgfcwnkpn';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 587;

        // Set sender and recipient
        $mail->setFrom('ridhswork17@gmail.com', 'Bookly.');
        $mail->addAddress($email, $name);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'New Enquiry - Book Store';
        $mail->Body = "<h3>Hello, you got a new enquiry</h3>
            <h4>Name: $name</h4>
            <h4>Email: $email</h4>
            <h4>Number: $number</h4>
            <h4>Message: $message</h4>";

            if ($mail->send()) {
                $_SESSION['message'] = 'Message sent successfully!';
            } else {
                $_SESSION['message'] = 'Message could not be sent!';
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "Mailer Error: {$mail->ErrorInfo}";
        }
    
        // Redirect back to contact.php
        header('location: contact.php');
        exit();
    } else {
        $_SESSION['message'] = 'Form data not received!';
        header('location: contact.php');
        exit();
    }
?>