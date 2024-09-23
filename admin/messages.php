<?php
include '../components/connect.php';

if(isset($_COOKIE['admin_id'])){
   $admin_id = $_COOKIE['admin_id'];
}else{
   $admin_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])) {
   // Retrieve and sanitize the delete_id from the POST request
   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);
   // Verify if the message with the given id exists in the database
   $verify_delete = $conn->prepare("SELECT * FROM `messages` WHERE id = ?");
   $verify_delete->execute([$delete_id]);
   // If the message exists, delete it from the `messages` table
   if($verify_delete->rowCount() > 0){
      $delete_bookings = $conn->prepare("DELETE FROM `messages` WHERE id = ?");
      $delete_bookings->execute([$delete_id]);
      // Success message if deletion is successful
      $success_msg[] = 'Message deleted!'; 
   }else{
      // Warning if the message was already deleted
      $warning_msg[] = 'Message deleted already!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- messages section starts  -->
<section class="grid">
   <h1 class="heading">messages</h1>
   <div class="box-container">
   <?php
      // Query the database to select all messages
      $select_messages = $conn->prepare("SELECT * FROM `messages`");
      $select_messages->execute();
      // Check if there are any messages in the database
      if($select_messages->rowCount() > 0) {
         // Loop through each message and display it
         while($fetch_messages = $select_messages->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box">
      <p>name : <span><?= $fetch_messages['name']; ?></span></p>
      <p>email : <span><?= $fetch_messages['email']; ?></span></p>
      <p>number : <span><?= $fetch_messages['number']; ?></span></p>
      <p>message : <span><?= $fetch_messages['message']; ?></span></p>
      <form action="" method="POST">
         <!-- Pass the message ID as a hidden input to delete -->
         <input type="hidden" name="delete_id" value="<?= $fetch_messages['id']; ?>">
         <input type="submit" value="delete message" onclick="return confirm('delete this message?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   } else {
   ?>
   <div class="box" style="text-align: center;">
      <p>no messages found!</p>
      <a href="dashboard.php" class="btn">Go to Home</a>
   </div>
   <?php
      }
   ?>
   </div>
</section>
<!-- messages section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>