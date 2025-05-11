<?php

@include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if(!isset($admin_id)){
   header('location:login.php');
};

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_users.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard</title>

   <!-- Font Awesome CDN link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom admin CSS file link -->
   <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php @include 'admin_header.php'; ?>

<section class="users">
   <h1 class="title">Users Account</h1>

   <div class="box-container" style="text-align: center; margin: 20px auto;">
   <table class="table" style="margin: 0 auto; border-collapse: collapse; width: 70%; text-align: left; font-size: 20px;">
      <thead>
         <tr style="background-color: #f2f2f2;">
            <th scope="col" style="padding: 12px; border: 1px solid #ddd;text-align:center;">User ID</th>
            <th scope="col" style="padding: 12px; border: 1px solid #ddd;text-align:center;">User Name</th>
            <th scope="col" style="padding: 12px; border: 1px solid #ddd;text-align:center;">Email</th>
            <th scope="col" style="padding: 12px; border: 1px solid #ddd;text-align:center;">User Type</th>
         </tr>
      </thead>
      <tbody>
         <?php
         $select_users = mysqli_query($conn, "SELECT * FROM `users`") or die('Query failed');
         if (mysqli_num_rows($select_users) > 0) {
            while ($fetch_users = mysqli_fetch_assoc($select_users)) {
         ?>
         <tr>
            <td style="padding: 6px; border: 1px solid #ddd;text-align:center;"><?php echo $fetch_users['id']; ?></td>
            <td style="padding: 6px; border: 1px solid #ddd;text-align:center;"><?php echo $fetch_users['name']; ?></td>
            <td style="padding: 6px; border: 1px solid #ddd;text-align:center;"><?php echo $fetch_users['email']; ?></td>
            <td style="padding: 6px; border: 1px solid #ddd;text-align:center;">
               <span style="color:<?php echo ($fetch_users['user_type'] == 'admin') ? 'var(--orange)' : 'inherit'; ?>">
                  <?php echo $fetch_users['user_type']; ?>
               </span>
            </td>
         </tr>
         <?php
            }
         } else {
            echo '<tr><td colspan="4" style="padding: 6px; text-align: center;">No users found</td></tr>';
         }
         ?>
      </tbody>
   </table>
</div>



</section>

<script src="js/admin_script.js"></script>
</body>
</html>
