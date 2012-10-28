<?php
	require("config.php");
	$_pulses=array(); $i=0;

	class PulseDB extends SQLite3 { function __construct() { $this->open(SQLITE_DB); } }

	$_database=new PulseDB();
		$_query=$_database->query("select datetime(stamp,'unixepoch','localtime') as blink from pulse where blink like '".date('Y-m-d')." %'");
		while($_result=$_query->fetchArray(SQLITE3_ASSOC))
		{ $_pulses[$i]=$_result["blink"]; $i++; }
	$_database->close();

	$_hours=array(); $_consumption=array();
	for($i=0;$i<date('G');$i++)
	{
		$h=($i<10?"0".$i:$i);
		$_group=array(); $j=0;
		for($k=0;$k<count($_pulses);$k++)
		{
			if(substr($_pulses[$k],11,2)==$h) { $_group[$j]=$_pulses[$k]; $j++; }
		}
		if(count($_group)>0)
		{
			$_hours[$i]=$_group;
			$_time=3600/count($_group);
			$_consumption[$i]=round((3600/($_time*IMPKWH)),4);
                } else {
                        $_consumption[$i]=0;
                }
	}

	$js="var consumption=[";
	for($i=0;$i<count($_consumption);$i++)
	{
		$h=($i<10?"0".$i:$i);
		$js.="[(new Date(\"".date('Y/m/d')." ".$h.":00:00\")).getTime(), ".$_consumption[$i]."]";
		if($i!=(count($_consumption)-1)) { $js.=","; }
	}
	$js.="];\n";
?>
<!doctype html>
	<html>
		<head>
			<meta charset="utf-8"/>
			<title>Raspberry Pi Energy Monitor</title>
			<meta name="description" content="Simple electricity consumption monitor."/>
			<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
			<link rel="stylesheet" href="styles/2col.css"/>
			<link rel="stylesheet" href="styles/2col-pro.css"/>
			<script src="scripts/libs/modernizr.2.6.2.min.js"></script>
		</head>
		<body>
			<div class="row">
				<div class="two columns">
					<h1>Raspberry Pi Energy Monitor</h1>
					<h2>Power Consumption Today</h2>
					<div id="graph"></div>
				</div>
			</div>
			<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
			<script src="scripts/libs/jquery.flot.js"></script>
			<script>
				$(function() {
					<?php print($js); ?>
					function showkwh(v,axis) { return v.toFixed(axis.tickDecimals)+"kWh"; }
					function doPlot(position) {
						$.plot($("#graph"),
							[ { data: consumption, color: '#1275c2', label: 'Power Used', lines: { show: true }, points: { show: true }, yaxis: 1 } ],
							{ xaxes: [ { mode: 'time', timeformat: '%h',
								min: (new Date("<?php print(date("Y/m/d 00:00:00")); ?>")).getTime(),
								max: (new Date("<?php print(date("Y/m/d 23:59:59")); ?>")).getTime(),
								ticks: 24, tickLength: 1, minTickSize: [1, 'hour'], reserveSpace: true } ],
							  yaxes: [ { min: 0, alignTicksWithAxis: null, position: 'left', tickFormatter: showkwh } ],
							  legend: { show: false },
							  grid: { hoverable: false, clickable: false, autoHighlight: false }
							}
						);
					}
					doPlot("right");
				});
			</script>
		</body>
	</html>
