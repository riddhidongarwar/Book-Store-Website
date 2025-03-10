<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:login.php');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if(isset($_POST['update_order'])) {

    $order_update_id = $_POST['order_id'];
    $update_payment = $_POST['update_payment'];
    mysqli_query($conn,"UPDATE orders SET payment_status = '$update_payment' WHERE id='$order_update_id'") or die('query failed');
    $message[]='Payment status has been updated';  

    $order_query = mysqli_query($conn, "SELECT * FROM orders WHERE id = '$order_update_id'") or die('query failed');
    $order_data = mysqli_fetch_assoc($order_query);
    $user_email = $order_data['email'];
    $user_name = $order_data['name'];



    if ($update_payment == 'completed') {
       
        $mail = new PHPMailer(true);
 
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'ridhswork17@gmail.com'; 
            $mail->Password = 'ofapatxrgfcwnkpn';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
 
            $mail->setfrom('ridhswork17@gmail.com', 'Bookly');
            $mail->addAddress($user_email, $user_name);
 
            $mail->isHTML(true);
            $mail->Subject = 'Order Completed!';
            $mail->Body = "<h3>Hello $user_name,</h3>
                           <p>Your order #$order_update_id has been marked as <strong>Completed</strong>.</p>
                           <p>Thank you for shopping with us!</p>";
 
            $mail->send();
        } catch (Exception $e) {
            error_log("Email not sent. Error: " . $mail->ErrorInfo);
        }
    }
}



if(isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn,"DELETE FROM orders WHERE id = '$delete_id'") or die('query failed');
    header('location:admin_orders.php');
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders</title>

    <!--font awesome css-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!--custom admin css file link-->
    <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>
    <?php
    include 'admin_header.php';
    ?>

    <!--Orders Section --->
    <section class="orders">
            <h1 class="title">Placed Orders</h1>

            <div class="box-container">
                <?php
                $select_orders = mysqli_query($conn,"SELECt * FROM orders") or die  ('query failed');
                if(mysqli_num_rows($select_orders) > 0) {
                    while($fetch_orders = mysqli_fetch_assoc($select_orders)) {
                ?>
                <div class="box">
                        <p> User ID : <span><?php echo $fetch_orders['user_id']; ?></span></p>
                        <p> Placed On : <span><?php echo $fetch_orders['placed_on']; ?></span></p>
                        <p> Name : <span><?php echo $fetch_orders['name']; ?></span></p>
                        <p> Number : <span><?php echo $fetch_orders['number']; ?></span></p>
                        <p> Email : <span><?php echo $fetch_orders['email']; ?></span></p>
                        <p> Address : <span><?php echo $fetch_orders['address']; ?></span></p>
                        <p> Total Products : <span><?php echo $fetch_orders['total_products']; ?></span></p>
                        <p> Total Price : <span>₹ <?php echo $fetch_orders['total_price']; ?>/-</span></p>
                        <p> Payment Method : <span><?php echo $fetch_orders['method']; ?></span></p>
                        <form action="" method="post">
                            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                            <select name="update_payment">
                                <option value="" selected disabled><?php echo $fetch_orders['payment_status']; ?></option>
                                <option value="pending">pending</option>
                                <option value="completed">completed</option>
                            </select>
                            <input type="submit" value="update order" name="update_order" class="option-btn">
                            <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('Delete this order?');" class="delete-btn">Delete Order</a>
                        </form>

                    </div>
                <?php
                 }
                } else {
                    echo '<p class="empty">No orders Placed yet !</p>';
                }
            
            
                ?>

            </div>
            
            

    </section>











    <!---custome admin js file link---->
    <script src="js/admin_script.js"></script>

</body>

</html>