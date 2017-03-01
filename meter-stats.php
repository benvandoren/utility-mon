<?php
include "./meterParams.php";

if(isset($_POST['id'])) {
  $mId = $_POST['id'];
}

// connect to db
include "./dbConnect.php"; // $link = mysqli_connect("hostname", "utility_mon", "password", "UtilityMon"); ...

$mPrimaryKey  = "";
$mPrimaryKey2 = "";

// most recent data
$mostRecentQ    = $link->query("SELECT mPrimaryKey, mTime, mTotalConsumption, mConsumed from UtilityMeter WHERE mPrimaryKey = (SELECT max(mPrimaryKey) FROM UtilityMeter WHERE mId = $mId);");
// second most recent
$nextRecentQ    = $link->query("SELECT mPrimaryKey, mTime, mTotalConsumption, mConsumed from UtilityMeter WHERE mId = $mId order by mPrimaryKey desc LIMIT 1,1;");
// very first data point
$firstQ         = $link->query("SELECT mTime, mTotalConsumption from UtilityMeter WHERE mPrimaryKey = (SELECT min(mPrimaryKey) FROM UtilityMeter WHERE mId = $mId);");
// average of all data points
$avgUseQ        = $link->query("SELECT mType, avg(mConsumed), count(mPrimaryKey) FROM UtilityMeter WHERE mId = $mId;");

if(!$mostRecentQ || !$avgUseQ || !$firstQ || !$nextRecentQ) {
  die("Query Failed: ".$link->error);
}

// Fetch Queries:
// get avg (incremental) use calculated by mysql
$row=$avgUseQ->fetch_array(MYSQLI_ASSOC);
$mType  = $row['mType'];
$avgUse = $row['avg(mConsumed)']; // average incremental use
$pktsRecv           = $row['count(mPrimaryKey)']; // get count here because we already have the full list (length)

// Get most recent data
$row=$mostRecentQ->fetch_array(MYSQLI_ASSOC);
$mPrimaryKey        = $row['mPrimaryKey'];
$mConsumed          = $row['mConsumed'];
$lastReading        = $row['mTotalConsumption'];
$lastTime           = $row['mTime'];
$lastTimeDateFormat = date('Y-m-d H:i:s', $lastTime);

// get next most recent
$row=$nextRecentQ->fetch_array(MYSQLI_ASSOC);
$nextMConsumed    = $row['mConsumed'];
// check if it's set; if it isn't (only one data point) make it equal to the only available value
if ($nextMConsumed == 0) {
  $nextMConsumed = $mConsumed;
}

// get first use so we can calculate regular average
$row=$firstQ->fetch_array(MYSQLI_ASSOC);
$firstReading = $row['mTotalConsumption'];
$firstTime    = $row['mTime'];

// calculate values
$totalTimeDiff    = $lastTime - $firstTime;
// prevent math errors if 0
if ($totalTimeDiff == 0) {
  $totalTimeDiff = 1;
}
$totalReadingDiff = $lastReading - $firstReading;
$consumedDiff     = $mConsumed - $nextMConsumed;
if ($consumedDiff > 0) {
  $diff = "increase";
} elseif ($consumedDiff < 0) {
  $diff = "decrease";
} elseif ($consumedDiff == 0) {
  $diff = "noChange";
}

echo "<div id='customer'>";
echo "Customer ID: <span id='mId'>$mId</span>";

// determine types
$mTypeStr = "";
if(in_array($mType, $electricMeterTypes)) {
  $mTypeStr       = "Electric";
  $totalPowerDiff = $totalReadingDiff; // Wh
  $mConsumed      = sprintf("%.2f", $mConsumed);
  $avgUse         = sprintf("%.2f", $avgUse);
  $totalWatts     = sprintf("%.2f", $totalPowerDiff * 3600 / $totalTimeDiff);

  echo "<br/>$mTypeStr Meter ($mType)";
  echo "</div>"; // end customer id
  echo "<div id='meterContent'>";
  echo "<div id='utilityData'> <span class='$diff bold'>Current Use:  <span id='mConsumed'>$mConsumed</span> Watts</span>";
  echo "<br/>Avg Incremental Use: $avgUse Watts";
  echo "<br/>Avg Use: $totalWatts Watts";
  echo "</div>"; // end utilityData id
  echo "<div id='sdrData'>";
  echo "<br/>$pktsRecv Data Points";
  echo "<br/>Last Data Point Collected: $lastTimeDateFormat";
  echo "</div>"; // end sdrData id
  echo "</div>"; // end meterContent id

} elseif(in_array($mType, $gasMeterTypes)) {
  $mTypeStr = "Gas";
  $totalGasDiff = $totalReadingDiff;
  $mConsumed    = sprintf("%.4f", $mConsumed);
  $avgUse       = sprintf("%.4f", $avgUse);
  $totalGas     = sprintf("%.4f", $totalGasDiff / $totalTimeDiff); // Cubic feet / sec ??

  echo "<br/>$mTypeStr Meter ($mType)";
  echo "</div>"; // end customer id
  echo "<div id='meterContent'>";
  echo "<div id='utilityData'> <span class='$diff bold'>Current Use:  <span id='mConsumed'>$mConsumed</span> Cubic Feet / Sec</span>";
  echo "<br/> Avg Incremental Use: $avgUse Cubic Feet / Sec";
  echo "<br/> Avg Use: $totalGas Cubic Feet / Sec";
  echo "</div>"; // end utilityData id
  echo "<div id='sdrData'>";
  echo "<br/>$pktsRecv Data Points";
  echo "<br/>Last Data Point Collected: $lastTimeDateFormat";
  echo "</div>"; // end sdrData id
  echo "</div>"; // end meterContent id
} elseif(in_array($mType, $waterMeterTypes)) {
  $mTypeStr = "Water";
  $totalWaterDiff = $totalReadingDiff;
  $mConsumed      = sprintf("%.4f", $mConsumed);
  $avgUse         = sprintf("%.4f", $avgUse);
  $totalWater     = sprintf("%.4f", $totalWaterDiff / $totalTimeDiff); // Cubic feet / sec ??

  echo "<br/>$mTypeStr Meter ($mType)";
  echo "</div>"; // end customer id
  echo "<div id='meterContent'>";
  echo "<div id='utilityData'> <span class='$diff bold'>Current Use:  <span id='mConsumed'>$mConsumed</span> Cubic Feet / Sec</span>";
  echo "<br/>Avg Incremental Use: $avgUse Cubic Feet / Sec";
  echo "<br/>Avg Use: $totalWater Cubic Feet / Sec";
  echo "</div>"; // end utilityData id
  echo "<div id='sdrData'>";
  echo "<br/>$pktsRecv Data Points";
  echo "<br/>Last Data Point Collected: $lastTimeDateFormat";
  echo "</div>"; // end sdrData id
  echo "</div>"; // end meterContent id
}
?>
