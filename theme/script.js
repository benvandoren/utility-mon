//
var meterStats = $("#meterStats");
var mId = $("#mId").text();
setInterval(function () {
  meterStats.load("/utility-mon/meter-stats.php", {id:mId});
}, 300000); //300000 ; 5 minutes
