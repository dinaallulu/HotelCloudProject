<?php

include '../components/connect.php';

// Check if 'admin_id' cookie exists (meaning the admin is logged in)
if(isset($_COOKIE['admin_id'])){
   // If the cookie exists, store the value in $admin_id
   $admin_id = $_COOKIE['admin_id'];
}else{
   // If the cookie does not exist, redirect to the login page
   $admin_id = '';
   header('location:login.php');
}

// Check if the form is submitted using the 'submit' button
if(isset($_POST['submit'])){
   // Create a unique ID for the new admin
   $id = create_unique_id();
   // Get and sanitize the admin name from the form input
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING); 
   // Get the password and confirm password from the form, and hash them using sha1 for security
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING); 
   $c_pass = sha1($_POST['c_pass']);
   $c_pass = filter_var($c_pass, FILTER_SANITIZE_STRING);   

   // Check if an admin with the same name already exists in the 'admins' table
   $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = ?");
   $select_admins->execute([$name]);

   // If a matching admin is found, display a warning message
   if($select_admins->rowCount() > 0){
      $warning_msg[] = 'Username already taken!';
   }else{
      // Check if the passwords match
      if($pass != $c_pass){
         // If the passwords don't match, display a warning message
         $warning_msg[] = 'Password not matched!';
      }else{
         // If passwords match, insert the new admin into the 'admins' table
         $insert_admin = $conn->prepare("INSERT INTO `admins`(id, name, password) VALUES(?,?,?)");
         $insert_admin->execute([$id, $name, $c_pass]);
         // Display a success message upon successful registration
         $success_msg[] = 'Registered successfully!';
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
   
<!-- header section starts  -->
<?php include '../components/admin_header.php'; ?>
<!-- header section ends -->

<!-- register section starts  -->
<section class="form-container">
   <form action="" method="POST">
      <h3>register new</h3>
      <input type="text" name="name" placeholder="enter username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" placeholder="enter password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="c_pass" placeholder="confirm password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="register now" name="submit" class="btn">
   </form>
</section>
<!-- register section ends -->


<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<!-- custom js file link  -->
<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>