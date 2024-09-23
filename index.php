<?php
// Include the database connection script
include 'components/connect.php';

// Check if a 'user_id' cookie is set
if (isset($_COOKIE['user_id'])) {
   // If the cookie exists, retrieve the user_id from the cookie
   $user_id = $_COOKIE['user_id'];
} else {
    // If the cookie doesn't exist, set a new cookie with a unique ID
   setcookie('user_id', create_unique_id(), time() + 60 * 60 * 24 * 30, '/');
   // Redirect the user to the index page after setting the cookie
   header('location:index.php');
}

// Check if the "check" button is submitted (for checking room availability)
if (isset($_POST['check'])) {

   // Retrieve and sanitize the check-in date from the POST request
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);

   // Initialize total_rooms to 0, to count the number of rooms booked on the selected date
   $total_rooms = 0;

   // Prepare a query to select all bookings with the specified check-in date
   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   // Loop through all the fetched bookings and add the number of rooms to total_rooms
   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   // If total rooms booked on that date exceed or meet the limit (30), show a warning message
   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {
      // If rooms are available, show a success message
      $success_msg[] = 'rooms are available';
   }
}


// Check if the "book" button is submitted (for booking a room)
if (isset($_POST['book'])) {

   // Generate a unique booking ID
   $booking_id = create_unique_id();

   // Retrieve and sanitize user input data (name, email, number, rooms, check-in, etc.)
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $rooms = $_POST['rooms'];
   $rooms = filter_var($rooms, FILTER_SANITIZE_STRING);
   $check_in = $_POST['check_in'];
   $check_in = filter_var($check_in, FILTER_SANITIZE_STRING);
   $check_out = $_POST['check_out'];
   $check_out = filter_var($check_out, FILTER_SANITIZE_STRING);
   $adults = $_POST['adults'];
   $adults = filter_var($adults, FILTER_SANITIZE_STRING);
   $childs = $_POST['childs'];
   $childs = filter_var($childs, FILTER_SANITIZE_STRING);

   // Reset total_rooms for this booking check
   $total_rooms = 0;

   // Check the number of bookings for the selected check-in date
   $check_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE check_in = ?");
   $check_bookings->execute([$check_in]);

   // Sum the number of rooms booked on that day
   while ($fetch_bookings = $check_bookings->fetch(PDO::FETCH_ASSOC)) {
      $total_rooms += $fetch_bookings['rooms'];
   }

   // If rooms are fully booked (30 or more rooms), show a warning message
   if ($total_rooms >= 30) {
      $warning_msg[] = 'rooms are not available';
   } else {
      // Verify if the same booking already exists (to prevent duplicate bookings)
      $verify_bookings = $conn->prepare("SELECT * FROM `bookings` WHERE user_id = ? AND name = ? AND email = ? AND number = ? AND rooms = ? AND check_in = ? AND check_out = ? AND adults = ? AND childs = ?");
      $verify_bookings->execute([$user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

      if ($verify_bookings->rowCount() > 0) {
         // If a matching booking is found, show a warning message
         $warning_msg[] = 'room booked alredy!';
      } else {
         // Insert the booking into the database
         $book_room = $conn->prepare("INSERT INTO `bookings`(booking_id, user_id, name, email, number, rooms, check_in, check_out, adults, childs) VALUES(?,?,?,?,?,?,?,?,?,?)");
         $book_room->execute([$booking_id, $user_id, $name, $email, $number, $rooms, $check_in, $check_out, $adults, $childs]);

         // Show a success message for the booking
         $success_msg[] = 'room booked successfully!';
      }
   }
}

// Check if the "send" button is submitted (for sending a message)
if (isset($_POST['send'])) {

   // Generate a unique message ID
   $id = create_unique_id();

   // Retrieve and sanitize user input data for the message (name, email, number, message)
   $name = $_POST['name'];
   $name = filter_var($name, FILTER_SANITIZE_STRING);
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   $number = $_POST['number'];
   $number = filter_var($number, FILTER_SANITIZE_STRING);
   $message = $_POST['message'];
   $message = filter_var($message, FILTER_SANITIZE_STRING);

   // Check if the same message has already been sent (to prevent duplicates)
   $verify_message = $conn->prepare("SELECT * FROM `messages` WHERE name = ? AND email = ? AND number = ? AND message = ?");
   $verify_message->execute([$name, $email, $number, $message]);

   if ($verify_message->rowCount() > 0) {
      // If a matching message is found, show a warning message
      $warning_msg[] = 'message sent already!';
   } else {
      // Insert the new message into the database
      $insert_message = $conn->prepare("INSERT INTO `messages`(id, name, email, number, message) VALUES(?,?,?,?,?)");
      $insert_message->execute([$id, $name, $email, $number, $message]);
      // Show a success message for the message
      $success_msg[] = 'message send successfully!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>StaySuite</title>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">
   <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>

<body>

   <?php include 'components/user_header.php'; ?>

   <!-- home section starts  -->
   <section class="home" id="home">
      <div class="swiper home-slider">
         <div class="swiper-wrapper">
            <div class="box swiper-slide">
               <img src="images/home-img-1.jpg" alt="">
               <div class="flex">
                  <h3>Luxurious Rooms</h3>
                  <a href="#availability" class="btn">Check Availability</a>
               </div>
            </div>
            <div class="box swiper-slide">
               <img src="images/home-img-2.jpg" alt="">
               <div class="flex">
                  <h3>foods and drinks</h3>
                  <a href="#reservation" class="btn">Make a Reservation</a>
               </div>
            </div>
            <div class="box swiper-slide">
               <img src="images/home-img-3.jpg" alt="">
               <div class="flex">
                  <h3>Luxurious Halls</h3>
                  <a href="#contact" class="btn">Contact Us</a>
               </div>
            </div>
         </div>
         <div class="swiper-button-next"></div>
         <div class="swiper-button-prev"></div>
      </div>
   </section>
   <!-- home section ends -->

   <!-- availability section starts  -->
   <section class="availability" id="availability">
      <form action="" method="post">
         <div class="flex">
            <div class="box">
               <p>check in <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>check out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>adults <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1">1 adult</option>
                  <option value="2">2 adults</option>
                  <option value="3">3 adults</option>
                  <option value="4">4 adults</option>
                  <option value="5">5 adults</option>
                  <option value="6">6 adults</option>
               </select>
            </div>
            <div class="box">
               <p>childs <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="-">0 child</option>
                  <option value="1">1 child</option>
                  <option value="2">2 childs</option>
                  <option value="3">3 childs</option>
                  <option value="4">4 childs</option>
                  <option value="5">5 childs</option>
                  <option value="6">6 childs</option>
               </select>
            </div>
            <div class="box">
               <p>rooms <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1">1 room</option>
                  <option value="2">2 rooms</option>
                  <option value="3">3 rooms</option>
                  <option value="4">4 rooms</option>
                  <option value="5">5 rooms</option>
                  <option value="6">6 rooms</option>
               </select>
            </div>
         </div>
         <input type="submit" value="check availability" name="check" class="btn">
      </form>
   </section>
   <!-- availability section ends -->

   <!-- about section starts  -->
   <section class="about" id="about">
      <div class="row">
         <div class="image">
            <img src="images/about-img-1.jpg" alt="">
         </div>
         <div class="content">
            <h3>Best Staff</h3>
            <p>Meet our exceptional team of dedicated professionals who ensure every stay is a memorable one. From our attentive
               front desk staff to our expert housekeeping and culinary teams, each member is committed to providing the highest
               level of service and hospitality.</p>
            <a href="#reservation" class="btn">Make a Reservation</a>
         </div>
      </div>
      <div class="row revers">
         <div class="image">
            <img src="images/about-img-2.jpg" alt="">
         </div>
         <div class="content">
            <h3>Best Foods</h3>
            <p>Indulge in a culinary journey at our hotel with dishes crafted from the freshest ingredients by our talented chefs.
               From gourmet international cuisines to local delicacies, every bite is a celebration of flavor. Our menu offers
               something for everyone, ensuring a memorable dining experience.</p>
            <a href="#contact" class="btn">Contact Us</a>
         </div>
      </div>
      <div class="row">
         <div class="image">
            <img src="images/about-img-3.jpg" alt="">
         </div>
         <div class="content">
            <h3>Swimming Pool</h3>
            <p>Escape and unwind at our luxurious swimming pool, where you can soak up the sun in a serene and relaxing atmosphere.
               Whether you're enjoying a refreshing dip or lounging poolside with a drink, our pool offers the perfect retreat for
               relaxation during your stay.</p>
            <a href="#availability" class="btn">Check Availability</a>
         </div>
      </div>
   </section>
   <!-- about section ends -->

   <!-- services section starts  -->
   <section class="services">
      <div class="box-container">
         <div class="box">
            <img src="images/icon-1.jpeg" alt="">
            <h3>Food & Drinks</h3>
            <p>Delicious food and drinks, available anytime for your enjoyment.</p>
         </div>

         <div class="box">
            <img src="images/icon-2.jpeg" alt="">
            <h3>Outdoor Dining</h3>
            <p>Experience delightful outdoor dining with stunning views and fresh air at our hotel.</p>
         </div>

         <div class="box">
            <img src="images/icon-3.jpeg" alt="">
            <h3>Beach View</h3>
            <p>Enjoy beachside outdoor dining with breathtaking ocean views at our hotel.</p>
         </div>

         <div class="box">
            <img src="images/icon-4.jpeg" alt="">
            <h3>Decorations</h3>
            <p>Elegant decor combines modern luxury with classic charm for a welcoming ambiance.</p>
         </div>

         <div class="box">
            <img src="images/icon-5.jpeg" alt="">
            <h3>Swimming Pool</h3>
            <p>Enjoy our stunning swimming pool, a perfect blend of relaxation and luxury.</p>
         </div>

         <div class="box">
            <img src="images/icon-6.jpeg" alt="">
            <h3>Resort Beach</h3>
            <p>Relax on our pristine resort beach, where sun, sand, and serenity await.</p>
         </div>
      </div>
   </section>
   <!-- services section ends -->

   <!-- reservation section starts  -->
   <section class="reservation" id="reservation">
      <form action="" method="post">
         <h3>Make a Reservation</h3>
         <div class="flex">
            <div class="box">
               <p>your name <span>*</span></p>
               <input type="text" name="name" maxlength="50" required placeholder="enter your name" class="input">
            </div>
            <div class="box">
               <p>your email <span>*</span></p>
               <input type="email" name="email" maxlength="50" required placeholder="enter your email" class="input">
            </div>
            <div class="box">
               <p>your number <span>*</span></p>
               <input type="number" name="number" maxlength="10" min="0" max="9999999999" required placeholder="enter your number" class="input">
            </div>
            <div class="box">
               <p>rooms <span>*</span></p>
               <select name="rooms" class="input" required>
                  <option value="1" selected>1 room</option>
                  <option value="2">2 rooms</option>
                  <option value="3">3 rooms</option>
                  <option value="4">4 rooms</option>
                  <option value="5">5 rooms</option>
                  <option value="6">6 rooms</option>
               </select>
            </div>
            <div class="box">
               <p>check in <span>*</span></p>
               <input type="date" name="check_in" class="input" required>
            </div>
            <div class="box">
               <p>check out <span>*</span></p>
               <input type="date" name="check_out" class="input" required>
            </div>
            <div class="box">
               <p>adults <span>*</span></p>
               <select name="adults" class="input" required>
                  <option value="1" selected>1 adult</option>
                  <option value="2">2 adults</option>
                  <option value="3">3 adults</option>
                  <option value="4">4 adults</option>
                  <option value="5">5 adults</option>
                  <option value="6">6 adults</option>
               </select>
            </div>
            <div class="box">
               <p>childs <span>*</span></p>
               <select name="childs" class="input" required>
                  <option value="0" selected>0 child</option>
                  <option value="1">1 child</option>
                  <option value="2">2 childs</option>
                  <option value="3">3 childs</option>
                  <option value="4">4 childs</option>
                  <option value="5">5 childs</option>
                  <option value="6">6 childs</option>
               </select>
            </div>
         </div>
         <input type="submit" value="book now" name="book" class="btn">
      </form>
   </section>
   <!-- reservation section ends -->

   <!-- gallery section starts  -->
   <section class="gallery" id="gallery">
      <div class="swiper gallery-slider">
         <div class="swiper-wrapper">
            <img src="images/gallery-img-1.jpg" class="swiper-slide" alt="">
            <img src="images/gallery-img-2.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-3.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-4.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-5.webp" class="swiper-slide" alt="">
            <img src="images/gallery-img-6.webp" class="swiper-slide" alt="">
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
   <!-- gallery section ends -->

   <!-- contact section starts  -->
   <section class="contact" id="contact">
      <div class="row">
         <form action="" method="post">
            <h3>Send Us Message</h3>
            <input type="text" name="name" required maxlength="50" placeholder="enter your name" class="box">
            <input type="email" name="email" required maxlength="50" placeholder="enter your email" class="box">
            <input type="number" name="number" required maxlength="10" min="0" max="9999999999" placeholder="enter your number" class="box">
            <textarea name="message" class="box" required maxlength="1000" placeholder="enter your message" cols="30" rows="10"></textarea>
            <input type="submit" value="send message" name="send" class="btn">
         </form>
      </div>
   </section>
   <!-- contact section ends -->

   <!-- reviews section starts  -->
   <section class="reviews" id="reviews">
      <div class="swiper reviews-slider">
         <div class="swiper-wrapper">
            <div class="swiper-slide box">
               <img src="images/pic-1.png" alt="">
               <h3>Kareem Mohammed</h3>
               <p>The website made planning my trip so easy. The hotel was exactly as advertised, with beautiful rooms and excellent
                  service. I’m already planning my next visit!</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-2.png" alt="">
               <h3>Sarah Ali</h3>
               <p>An outstanding stay! The website was easy to navigate, and booking was a breeze. The hotel exceeded my expectations with
                  its luxurious rooms and exceptional service.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-3.png" alt="">
               <h3>Mohammed Ahmed</h3>
               <p>I had a wonderful experience from start to finish. The site provided clear information and beautiful photos that matched
                  perfectly with what I found upon arrival. Highly recommend</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-4.png" alt="">
               <h3>Rama Hossam</h3>
               <p>Great website and even better hotel. The detailed descriptions and photos helped me choose the perfect room, and the
                  on-site experience was fantastic. Five stars!.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-5.png" alt="">
               <h3>Zain Emad</h3>
               <p>The booking process was smooth, and the hotel did not disappoint. The amenities were top-notch, and the staff was
                  incredibly friendly. Will definitely return.</p>
            </div>
            <div class="swiper-slide box">
               <img src="images/pic-6.png" alt="">
               <h3>Linda Ali</h3>
               <p>A seamless booking experience and a stay to remember. The hotel's decor and facilities were just as impressive as
                  described online. Truly a gem.</p>
            </div>
         </div>
         <div class="swiper-pagination"></div>
      </div>
   </section>
   <!-- reviews section ends  -->

   <?php include 'components/footer.php'; ?>

   <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <?php include 'components/message.php'; ?>
</body>
</html>