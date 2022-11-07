<?php
echo "Hello there, this is a PHP Apache container";

//These are the defined authentication environment in the db service

// The MySQL service named in the docker-compose.yml.
$host = 'db';

// Database use name
$user = 'MYSQL_USER';

//database user password
$pass = 'MYSQL_PASSWORD';

// check the MySQL connection status
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connected to MySQL server successfully!";
}
?>


<html>

<head>
    <link href="calendar.css" type="text/css" rel="stylesheet" />
</head>

<body>
    <?php
    include 'Calendar.php';
    include 'Booking.php';
    include 'BookableCell.php';


    $booking = new Booking(
        'MYSQL_DATABASE',
        'db',
        'MYSQL_USER',
        'MYSQL_PASSWORD'
    );

    $bookableCell = new BookableCell($booking);

    $calendar = new Calendar();

    $calendar->attachObserver('showCell', $bookableCell);

    $bookableCell->routeActions();

    echo $calendar->show();
    ?>
</body>

</html>