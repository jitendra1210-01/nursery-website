<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
}

if(isset($_POST['update_order'])){
   $order_id = $_POST['order_id'];
   $update_payment = $_POST['update_payment'];
   mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment' WHERE id = '$order_id'") or die('query failed');
   $message[] = 'Payment status has been updated!';
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php');
}

$search_query = "";
if (isset($_POST['search'])) {
   $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
   
   $search_query = "WHERE 1";

   

   if ($product_name != '') {
       // Assuming `order_items` stores product names and `order_id` is the foreign key
       $search_query .= " AND oi.product_name LIKE '%$product_name%'";
   }

   if ($payment_status != '') {
       $search_query .= " AND o.payment_status LIKE '%$payment_status%'";
   }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php @include 'admin_header.php'; ?>

<section class="placed-orders">

   <h1 class="title">Placed Orders</h1>

   <!-- Filter form -->
   <div class="filter-form" style="text-align: center; margin: 20px auto;">
      <form method="POST" action="">
         <input type="text" name="product_name" placeholder="Search by Product Name" style="padding: 5px; font-size: 14px;">
         <select name="payment_status" style="padding: 5px; font-size: 14px;">
            <option value="">All Payment Status</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
         </select>
         <input type="submit" name="search" value="Search" style="padding: 5px 10px; cursor: pointer;">
      </form>
   </div>

   <div class="table-container" style="text-align: center; margin: 20px auto;">
   <?php
   // Modified query to join orders and order_items (assuming order_items is the correct table)
   $select_orders = mysqli_query($conn, "SELECT o.*, oi.product_name FROM `orders` o
                                         LEFT JOIN `order_items` oi ON o.id = oi.order_id
                                         $search_query") or die('query failed');
   if (mysqli_num_rows($select_orders) > 0) {
   ?>
   <table style="margin: 0 auto; border-collapse: collapse; width: 90%; text-align: left; font-size: 15px;">
      <thead>
         <tr style="background-color: #f2f2f2;">
            <th style="padding: 10px; border: 1px solid #ddd;">User ID</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Placed On</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Name</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Number</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Email</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Address</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Product Name</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Total Price</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Payment Method</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Payment Status</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Actions</th>
         </tr>
      </thead>
      <tbody>
         <?php
         while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
         ?>
         <tr>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['user_id']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['placed_on']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['name']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['number']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['email']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['address']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['product_name']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['total_price']; ?>/-</td>
            <td style="padding: 10px; border: 1px solid #ddd;"><?php echo $fetch_orders['method']; ?></td>
            <td style="padding: 10px; border: 1px solid #ddd;">
               <form action="" method="post">
                  <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                  <select name="update_payment" style="font-size: 14px; padding: 5px;">
                     <option disabled selected><?php echo $fetch_orders['payment_status']; ?></option>
                     <option value="pending">Pending</option>
                     <option value="completed">Completed</option>
                  </select>
            </td>
            <td style="padding: 10px; border: 1px solid #ddd;">
               <input type="submit" name="update_order" value="Update" style="font-size: 14px; padding: 5px 10px; margin-right: 5px; cursor: pointer;">
               <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>" style="font-size: 14px; padding: 5px 10px; background: #f44336; color: #fff; text-decoration: none; border-radius: 3px;" onclick="return confirm('Delete this order?');">Delete</a>
               </form>
            </td>
         </tr>
         <?php
         }
         ?>
      </tbody>
   </table>
   <?php
   } else {
      echo '<p class="empty">No orders placed yet!</p>';
   }
   ?>
</div>

</section>

<script src="js/admin_script.js"></script>

</body>
</html>
