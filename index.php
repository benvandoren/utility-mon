<?php
include "header.php";
include "meterParams.php";
?>
  <div id="content">
    <div class="table">
<?php
// connect to db
include "dbConnect.php"; // $link = mysqli_connect("hostname", "utility_mon", "password", "UtilityMon"); ...

// print a list of customer id's
$result   = $link->query("SELECT  mId, mType, avg(mConsumed) FROM UtilityMeter GROUP BY mId");
if($result) {
  echo $result->num_rows." Meters found";
} else {
  die("Query Failed: ".$link->error);
}

echo "<div class='row'>";
echo "<div class='cell'>Customer</div>";
echo "<div class='cell'>Meter Type</div>";
echo "<div class='cell'>Avg Use</div>";
echo "</div>";

while($row=$result->fetch_array(MYSQLI_ASSOC)) {
  $mId    = $row['mId'];
  $mType  = $row['mType'];
  $mAvg   = sprintf("%.2f", $row['avg(mConsumed)']);

  // determine type
  $mTypeStr = "";
  if(in_array($mType, $electricMeterTypes)) {
    $mTypeStr = "Electric";
    $mTypeUnit = "Watts";
  } elseif(in_array($mType, $gasMeterTypes)) {
    $mTypeStr = "Gas";
    $mTypeUnit = "Cubic Feet/Sec";
  } elseif(in_array($mType, $waterMeterTypes)) {
    $mTypeStr = "Water";
    $mTypeUnit = "Cubic Feet/Sec";
  }
  echo "<div class='row'>";
  echo "<div class='cell'><a href='meter.php/?id=$mId'>$mId</a></div>";
  echo "<div class='cell'>$mTypeStr</div>";
  echo "<div class='cell'>$mAvg $mTypeUnit</div>";
  echo "</div>";
}

?>
    </div>
  </div>
</body>
</html>
