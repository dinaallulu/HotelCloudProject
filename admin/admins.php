<?php

include '../components/connect.php';

// Check if the 'admin_id' cookie is set to verify if the user is logged in as admin
if (isset($_COOKIE['admin_id'])) {
   $admin_id = $_COOKIE['admin_id'];
} else {
   // If no admin is logged in, redirect to the login page
   $admin_id = '';
   header('location:login.php');
}


// Check if the 'delete' button has been clicked
if(isset($_POST['delete'])){
   // Retrieve the admin ID to be deleted and sanitize the input
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Verify if the admin with the provided ID exists in the database
   $verify_delete = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   // If the admin exists, proceed with the deletion
   if($verify_delete->rowCount() > 0) {
      // Prepare and execute the query to delete the admin from the 'admins' table
      $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
      $delete_admin->execute([$delete_id]);
      // Store a success message to indicate the admin was successfully deleted
      $success_msg[] = 'Admin deleted!';
   }else{
      $warning_msg[] = 'Admin deleted already!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- admins section starts  -->
<section class="grid">
   <h1 class="heading">admins</h1>
   <div class="box-container">
   <div class="box" style="text-align: center;">
      <p>create a new admin</p>
      <a href="register.php" class="btn">register now</a>
   </div>
   <?php
      // Prepare and execute a query to select all admins from the database
      $select_admins = $conn->prepare("SELECT * FROM `admins`");
      $select_admins->execute();
      // Check if any admins are found
      if($select_admins->rowCount() > 0) {
         // Loop through each admin fetched from the database
         while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){
   ?>
   <!-- Box for displaying each admin's details -->
   <div class="box" <?php if( $fetch_admins['name'] == 'admin'){ echo 'style="display:none;"'; } ?> >
      <!-- Display admin name -->
      <p>name : <span><?= $fetch_admins['name']; ?></span></p>
      <!-- Form to delete an admin -->
      <form action="" method="POST">
         <!-- Hidden input to pass the admin ID for deletion -->
         <input type="hidden" name="delete_id" value="<?= $fetch_admins['id']; ?>">
         <input type="submit" value="delete admins" onclick="return confirm('delete this admin?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   } else { }
   ?>
   </div>
</section>
<!-- admins section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>