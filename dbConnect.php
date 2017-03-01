// ReName this file to dbConnect.php fill in hostname (eg. localhost or 127.0.0.1) and password
<?php
$link = mysqli_connect("hostname", "utility_mon", "password", "UtilityMon");
if ( $link->connect_errno ) {
    die ("Could not connect: " . $link->connect_error);
  }
?>
