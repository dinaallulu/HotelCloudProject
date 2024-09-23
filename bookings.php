<?php
// Include the database connection file
include 'components/connect.php';

// Check if the 'user_id' cookie is set
if (isset($_COOKIE['user_id'])) {
   // If the 'user_id' cookie exists, retrieve the user ID from the cookie
   $user_id = $_COOKIE['user_id'];
} else {
   // If the cookie is not set, create a new one with a unique ID (using the create_unique_id function)
   setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30, '/'); // Expires in 30 days
   // Redirect the user to the index page after setting the cookie
   header('location:index.php');
}

// Check if the 'cancel' button was submitted via POST (triggering the cancellation process)
if (isset($_POST['cancel'])) {

   // Retrieve and sanitize the booking_id input from the form submission
   $booking_id = $_POST['booking_id'];
   $booking_id = filter_var($booking_id, FILTER_SANITIZE_STRING);

   // Prepare a query to check if the booking exists in the database using the provided booking ID
   $verify_booking = $conn->prepare("SELECT * FROM `bookings` WHERE booking_id = ?");
   $verify_booking->execute([$booking_id]);

   // Check if the booking exists (i.e., the query returned a result)
   if ($verify_booking->rowCount() > 0) {
      // If the booking exists, prepare a query to delete it from the database
      $delete_booking = $conn->prepare("DELETE FROM `bookings` WHERE booking_id = ?");
      $delete_booking->execute([$booking_id]);
      // Add a success message to indicate the booking was cancelled
      $success_msg[] = 'booking cancelled successfully!';
   } else {
      // If the booking doesn't exist or was already cancelled, show a warning message
      $warning_msg[] = 'booking cancelled already!';
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
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>
   <?php include 'components/user_header.php'; ?>

   <!-- booking section starts  -->
   <section class="bookings">
      <h1 class="heading">My Bookings</h1>
      <div class="box-container">

         <?php
         // Prepare a SQL query to select all bookings where the user_id matches the current user
         $select_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ?");
         // Execute the query using the user's ID, which is passed as a parameter
         $select_bookings->execute([$user_id]);
         // Check if any bookings exist for the current user by counting the rows
         if ($select_bookings->rowCount() > 0) {
            // If there are bookings, loop through each booking using a while loop
            while ($fetch_booking = $select_bookings->fetch(PDO::FETCH_ASSOC)) {
         ?>

               <div class="box">
                  <p>name : <span><?= $fetch_booking['name']; ?></span></p>
                  <p>email : <span><?= $fetch_booking['email']; ?></span></p>
                  <p>number : <span><?= $fetch_booking['number']; ?></span></p>
                  <p>check in : <span><?= $fetch_booking['check_in']; ?></span></p>
                  <p>check out : <span><?= $fetch_booking['check_out']; ?></span></p>
                  <p>rooms : <span><?= $fetch_booking['rooms']; ?></span></p>
                  <p>adults : <span><?= $fetch_booking['adults']; ?></span></p>
                  <p>childs : <span><?= $fetch_booking['childs']; ?></span></p>
                  <p>booking id : <span><?= $fetch_booking['booking_id']; ?></span></p>
                  <form action="" method="POST">
                     <input type="hidden" name="booking_id" value="<?= $fetch_booking['booking_id']; ?>">
                     <input type="submit" value="cancel booking" name="cancel" class="btn" onclick="return confirm('cancel this booking?');">
                  </form>
               </div>

            <?php
            }
         } else {
            ?>

            <div class="box" style="text-align: center;">
               <p style="padding-bottom: .5rem; text-transform:capitalize;">No Bookings Found!</p>
               <a href="index.php#reservation" class="btn">Book New</a>
            </div>

         <?php
         }
         ?>

      </div>
   </section>
   <!-- booking section ends -->

   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>

</body>
</html>