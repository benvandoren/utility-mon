<?php
include "meterParams.php";

if(isset($_GET['id'])) {
  $mId = $_GET['id'];
} else {
  header('location:index.php');
}
include "header.php";
echo "<span class='hidden' id='mId'>$mId</span>";
echo "<span id='meterStats'>"; // ajax
include "meter-stats.php"; // get the actual data
echo "</span>"; // end meterStats (ajax) id
?>

<script src="/utility-mon/theme/script.js"></script>
