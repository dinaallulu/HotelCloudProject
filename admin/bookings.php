<?php
include '../components/connect.php';

// Check if the 'admin_id' cookie is set (i.e., the admin is logged in)
if(isset($_COOKIE['admin_id'])){
   // If the cookie exists, store the admin ID from the cookie
   $admin_id = $_COOKIE['admin_id'];
}else{
   // If no admin is logged in, redirect to the login page
   $admin_id = '';
   header('location:login.php');
}

// Check if the delete action has been triggered by the form (via the 'delete' button)
if(isset($_POST['delete'])){
   // Get the 'delete_id' (booking ID) from the form and sanitize it to prevent harmful input
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   // Prepare and execute a query to verify if the booking exists in the database
   $verify_delete = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_delete->execute([$delete_id]);

   // If the booking exists in the database
   if($verify_delete->rowCount() > 0){
      // Prepare and execute a query to delete the booking based on the booking ID
      $delete_bookings = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_bookings->execute([$delete_id]);
      // Store a success message indicating the booking has been deleted
      $success_msg[] = 'Booking deleted!';
   }else{
      // If the booking does not exist, store a warning message indicating it's already deleted
      $warning_msg[] = 'Booking deleted already!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Bookings</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- bookings section starts  -->
<section class="grid">
   <h1 class="heading">bookings</h1>
   <div class="box-container">
   <?php
      // Prepare and execute a query to select all bookings from the database
      $select_bookings = $conn->prepare("SELECT * FROM `bookings`");
      $select_bookings->execute();

      // Check if there are any bookings found in the database
      if($select_bookings->rowCount() > 0) {
         // Loop through each booking fetched from the database
         while($fetch_bookings = $select_bookings->fetch(PDO::FETCH_ASSOC)){
   ?>
   <!-- Box for each individual booking -->
   <div class="box">
      <p>booking id : <span><?= $fetch_bookings['booking_id']; ?></span></p>
      <p>name : <span><?= $fetch_bookings['name']; ?></span></p>
      <p>email : <span><?= $fetch_bookings['email']; ?></span></p>
      <p>number : <span><?= $fetch_bookings['number']; ?></span></p>
      <p>check in : <span><?= $fetch_bookings['check_in']; ?></span></p>
      <p>check out : <span><?= $fetch_bookings['check_out']; ?></span></p>
      <p>rooms : <span><?= $fetch_bookings['rooms']; ?></span></p>
      <p>adults : <span><?= $fetch_bookings['adults']; ?></span></p>
      <p>childs : <span><?= $fetch_bookings['childs']; ?></span></p>
      <!-- Form to delete a booking -->
      <form action="" method="POST">
         <!-- Hidden input to pass the booking ID for deletion -->
         <input type="hidden" name="delete_id" value="<?= $fetch_bookings['booking_id']; ?>">
         <input type="submit" value="delete booking" onclick="return confirm('delete this booking?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   } else { // If no bookings are found
   ?>
   <div class="box" style="text-align: center;">
      <p>no bookings found!</p>
      <a href="dashboard.php" class="btn">go to home</a>
   </div>
   <?php
      }
   ?>
   </div>
</section>
<!-- bookings section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>