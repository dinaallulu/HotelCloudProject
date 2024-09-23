<?php
// Check if there are any success messages stored in the $success_msg array
if(isset($success_msg)){
   // Loop through each success message and display it using SweetAlert
   foreach($success_msg as $success_msg){
      // Output a SweetAlert with a success icon and the message text
      echo '<script>swal("'.$success_msg.'", "" ,"success");</script>';
   }
}

// Check if there are any warning messages stored in the $warning_msg array
if(isset($warning_msg)){
    // Loop through each warning message and display it using SweetAlert
   foreach($warning_msg as $warning_msg){
      // Output a SweetAlert with a warning icon and the message text
      echo '<script>swal("'.$warning_msg.'", "" ,"warning");</script>';
   }
}

// Check if there are any info messages stored in the $info_msg array
if(isset($info_msg)){
   // Loop through each info message and display it using SweetAlert
   foreach($info_msg as $success_msg){
      // Output a SweetAlert with an info icon and the message text
      echo '<script>swal("'.$info_msg.'", "" ,"info");</script>';
   }
}

// Check if there are any error messages stored in the $error_msg array
if(isset($error_msg)){
   // Loop through each error message and display it using SweetAlert
   foreach($error_msg as $error_msg){
      // Output a SweetAlert with an error icon and the message text
      echo '<script>swal("'.$error_msg.'", "" ,"error");</script>';
   }
}
?>