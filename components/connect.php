<?php
// Database configuration variables for connection: 
// The variable $db_name holds the information for the database connection string.
// It defines the database type (MySQL), host (localhost), and database name (hotel_db).
$db_name = 'mysql:host=localhost;dbname=hotel_db';

// The variable $db_user_name holds the database username ('root').
$db_user_name = 'root';

// The variable $db_user_pass holds the database password, which is empty in this case.
$db_user_pass = '';

// Creating a new PDO (PHP Data Objects) instance to connect to the MySQL database using the connection string, username, and password.
// $conn represents the active connection to the database.
$conn = new PDO($db_name, $db_user_name, $db_user_pass);

function create_unique_id()
{
   // A string of characters that will be used to randomly generate the unique ID.
   $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
   $rand = array();
   $length = strlen($str) - 1;

   for ($i = 0; $i < 20; $i++) {
      // Randomly select an index between 0 and $length, inclusive.
      $n = mt_rand(0, $length);
       // Append the character at the randomly selected index to the $rand array.
      $rand[] = $str[$n];
   }
   return implode($rand);
}
