<?php
include '../components/connect.php';

if(isset($_POST['submit'])){
   // Retrieve and sanitize the username from the form input
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING); 
   // Retrieve the password, hash it using sha1, and sanitize the input
   $pass = sha1($_POST['pass']);
   $pass = filter_var($pass, FILTER_SANITIZE_STRING); 

   // Prepare a query to select an admin with the given name and password (hashed) from the 'admins' table
   $select_admins = $conn->prepare("SELECT * FROM `admins` WHERE name = ? AND password = ? LIMIT 1");
   // Execute the query with the provided username and hashed password
   $select_admins->execute([$name, $pass]);
   // Fetch the resulting row as an associative array
   $row = $select_admins->fetch(PDO::FETCH_ASSOC);

   // Check if a matching admin was found (if the query returned any rows)
   if($select_admins->rowCount() > 0){
      // If the admin exists, set a cookie with the admin's ID and redirect to the dashboard
      setcookie('admin_id', $row['id'], time() + 60*60*24*30, '/');
      header('location:dashboard.php');
   }else{
      // If no admin is found, store a warning message indicating incorrect credentials
      $warning_msg[] = 'Incorrect username or password!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>

<!-- login section starts  -->
<section class="form-container" style="min-height: 100vh;">
   <form action="" method="POST">
      <h3>welcome back!</h3>
      <p>Default name = <span>admin</span> & password = <span>111</span></p>
      <input type="text" name="name" placeholder="enter username" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="password" name="pass" placeholder="enter password" maxlength="20" class="box" required oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="submit" value="login now" name="submit" class="btn">
   </form>
</section>
<!-- login section ends -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>