<?php

include 'config.php';

session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$user_id = $_SESSION['user_id'];
$message = [];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if(isset($_POST['send'])){

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $msg = mysqli_real_escape_string($conn, $_POST['message']);
 
    $select_message = mysqli_query($conn, "SELECT * FROM message WHERE name = '$name' AND email = '$email' AND number = '$number' AND message = '$msg'") or die('query failed');
    //email change
    
    if(mysqli_num_rows($select_message) > 0){
       $message[] = 'Message Sent Already!';
    }else{
        $insert_query = "INSERT INTO message(user_id, name, email, number, message) VALUES(?, ?, ?, ?, ?)";
       //mysqli_query($conn, "INSERT INTO message(user_id, name, email, number, message) VALUES('$user_id', '$name', '$email', '$number', '$msg')") or die('query failed');
       // email change again
        $stmt = mysqli_prepare($conn, $insert_query);

        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "issss", $user_id, $name, $email, $number, $msg);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            die('Query preparation failed: ' . mysqli_error($conn));
        }

        
        $mail = new PHPMailer(true);
        try {
         
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ridhswork17@gmail.com';
            $mail->Password =  'ofapatxrgfcwnkpn';

            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            
            $mail->setFrom('ridhswork17@gmail.com', 'Bookly');
            $mail->addAddress($email, $name);

           
            $mail->isHTML(true);
            $mail->Subject = 'Thank you for contacting us!';
            $mail->Body = "<h3>Hi $name,</h3><p>Thank you for reaching out! We have received your message and will get back to you soon.</p><p><strong>Your Message:</strong> $msg</p>";

            $mail->send();
       
            $message[] = 'Message sent successfully, and email notification sent!';
        }catch (Exception $e) {
        $message[] = "Message stored, but email not sent. Error: " . htmlspecialchars($mail->ErrorInfo);
        }
    }
 }

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>contact</title>

    <!--font awesome css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!--custom admin css file link-->
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Contact us</h3>
        <p> <a href="home.php">Home</a> / Contact </p>
    </div>

    <section class="contact">

        <form action="" method="post">
            <h3>say something!</h3>
            <input type="text" name="name" required placeholder="enter your name" class="box">
            <input type="email" name="email" required placeholder="enter your email" class="box">
            <input type="number" name="number" required placeholder="enter your number" class="box">
            <textarea name="message" class="box" placeholder="enter your message" id="" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" name="send" class="btn">
        </form>

    </section>













    <?php include('footer.php'); ?>

    <!---custome admin js file link---->
    <script src="js/script.js"></script>

</body>

</html>