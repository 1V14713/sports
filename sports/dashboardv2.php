<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'sports/config2.php';
require PUN_ROOT.'sports/include/fonctions.php';
require PUN_ROOT.'/rf/razorflow.php';


$now = new DateTime();
$end= new DateTime();
//$last3months = $end->sub(new DateInterval('P3M'));
//$start=$end->sub(new DateInterval('P2M'));
//$end= $now->add(new DateInterval('P7D'));

/*
$fseuil=150 ;
$Tca=7;
$Tcc=42;
$atl_b=0;
$ctl_b=0;
*/



//$link=connect_db($db_host, $db_username, $db_password, $db_name);

$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
if ($mysqli->connect_errno) {
    echo "Echec lors de la connexion à MySQL  : (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
$query_settings = "SELECT user, fseuil, Tca,Tcc,atl, ctl, start,end  FROM `perf_setting` WHERE user='mathieugravil'";
$result_settings=$mysqli->query($query_settings);
$result_settings->data_seek(0);
$settings=$result_settings->fetch_assoc() ;

$fseuil=$settings['fseuil'] ;
$Tca=$settings['Tca'];
$Tcc=$settings['Tcc'];
$atl_b=$settings['atl'];
$ctl_b=$settings['ctl'];
$start=$end->sub(new DateInterval($settings['start']));
$end= $now->add(new DateInterval($settings['end']));

$end_str=$end->format('Y-m-d');
$start_str=$start->format('Y-m-d');
$datetime1=$start ;

$query="select seance_id, name, sport_name, date, calories, distance, duration, 
		average/$fseuil  , (100*TIME_TO_SEC(duration)*average*average)/(3600*$fseuil*$fseuil), average, maximum, 
		 Vaverage, Vmaximum , altitude,TIME_TO_SEC(duration)
from seances, sport_type 
Where seances.sport_id = sport_type.sport_id
order by date asc";

//$result = mysql_query($query) or die("La requete  $query a echouee");
$result=$mysqli->query($query);
$num_rows = $result->num_rows;

$k=1;	
//$result->data_seek(0);

while ($row = $result->fetch_array(MYSQLI_NUM) )	
//while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
	if ($k == 1 )
	{	
	$datetime1=new DateTime($row[3]);
	$previousdate = new DateTime($row[3]);
	$total_duration_sec = 1;
	$barXtotal_dur_sec = 0;
	}

	$current_date=new DateTime($row[3]);
	$interval = $datetime1->diff($current_date);
	$m=$interval->format('%a');
	$temp=$datetime1;
	for ( $i = 1 ; $i<$m ; $i++ )
	{
//		$atl= $atl_b - $atl_b/$Tca;
//		$ctl= $ctl_b - $ctl_b/$Tcc;
$atl= $atl_b - $atl_b *(1-exp(-1/$Tca));
$ctl= $ctl_b - $ctl_b*(1-exp (-1/$Tcc));
	$tsb = number_format($ctl - $atl, 2) ;
	$atl_b=$atl ;
	$ctl_b=$ctl;
	$atl2=number_format($atl, 2);
	$ctl2=number_format($ctl, 2);
	$tsb = number_format($ctl - $atl, 2) ;

	$temp= $temp->add(new DateInterval('P1D'));
	$temp2=$temp->format('Y-m-d') ;
	$total_duration_sec = 1 ;
	$barXtotal_dur_sec = 0 ;
	if($temp > $start )
	{
		$data[]=array($temp->format('Y-m-d'),floatval($atl), floatval($ctl),floatval($tsb), 0, 0);
	}
	}
$interval2 = $previousdate->diff($current_date);
$m2=$interval2->format('%a');
if ($m2 == 0)
{
# On est sur le même jour..
$total_duration_sec = $total_duration_sec + $row[14];
$barXtotal_dur_sec = $barXtotal_dur_sec +  $row[9]*$row[14];
}
else
{
# si on est sur un autre jour
$total_duration_sec = $row[14];
$barXtotal_dur_sec = $row[9]*$row[14];
$previousdate = $current_date ;
}
$fmoy_day=$barXtotal_dur_sec/$total_duration_sec;

$if =  number_format($fmoy_day/$fseuil,2)  ;
$tss =  number_format((100*$total_duration_sec*$fmoy_day*$fmoy_day/$fseuil)/(3600*$fseuil),2);

$atl= $atl_b+($tss-$atl_b)/$Tca;
$ctl= $ctl_b +($tss-$ctl_b)/$Tcc;
$tsb = number_format($ctl - $atl, 2) ;
//	$atl= $atl_b+($row[8]-$atl_b)/$Tca;
//$ctl= $ctl_b +($row[8]-$ctl_b)/$Tcc;
//mgr $atl= $atl_b +($row[8]- $atl_b)*(1- exp(-1/$Tca));
//MGR $ctl= $ctl_b +($row[8] - $ctl_b)*(1-exp (-1/$Tcc));


$tsb = number_format($ctl - $atl, 2) ;
$atl_b=$atl ;
$ctl_b=$ctl;
$atl2=number_format($atl, 2);
$ctl2=number_format($ctl, 2);
$datetime1=$current_date;

$tss= number_format($row[8], 2);
$if=number_format($row[7], 2);
$temp2=$current_date->format('Y-m-d') ;
//print_r($current_date);

if($current_date > $start )
{
		$data[]=array($current_date->format('Y-m-d'),floatval($atl), floatval($ctl),floatval($tsb),floatval($if),floatval($tss));
}



 $k=$k+1;
}

//$now =  new DateTime();
	$interval = $datetime1->diff($end);
	$m=$interval->format('%a');
	for ( $i = 1 ; $i<$m+1 ; $i++ )
	{
$atl= $atl_b - $atl_b *(1-exp(-1/$Tca));
$ctl= $ctl_b - $ctl_b*(1-exp (-1/$Tcc));
	$tsb = number_format($ctl - $atl, 2) ;
	$atl_b=$atl ;
	$ctl_b=$ctl;
	$atl2=number_format($atl, 2);
	$ctl2=number_format($ctl, 2);
	$tsb = number_format($ctl - $atl, 2) ;

	$datetime1= $datetime1->add(new DateInterval('P1D'));
	$temp2=$datetime1->format('Y-m-d') ;
	$data[]=array($temp2,floatval($atl), floatval($ctl),floatval($tsb), 0, 0);
	
	}

mysqli_free_result($result_settings);
mysqli_free_result($result);
$mysqli->close();
//mysql_close($link);

$ds = new ArrayDataSource ();
$ds->setData(array(
'mydate' => 'text' ,
'atl' => 'number' ,
'ctl' => 'number' ,
'tsb' => 'number',
'if' => 'number' ,
'tss' => 'number'
),
 $data);
$ds->initialize();


$chart = new ChartComponent();
$chart->setCaption("Performance Management");
$chart->setWidth(4);
$chart->setHeight(4);
$chart->setDataSource($ds);

$chart->setLabelExpression("DATE ", 'mydate');
/*, array(
	'timestampRange' => false,
	'timeUnit' => 'day',
	'autoDrill' => false,
 'customTimeUnitPath' => array('month', 'day')
));
*/
$chart->setYAxis("ATL/CTL/TSB", array(
		'adaptiveYMin' => true
//	'minValue' => -50,
//	'maxValue' => 200
));

/*
$chart->setSecondYAxis("TSB", array(
	'minValue' => -50,
	'maxValue' => 50
));
*/
$chart->addSeries('ATL Fatigue',    'atl' ,array(
		'decimals' => 2 ,
		'aggregateFunction' => "AVG" )
); // Expression
$chart->addSeries('CTL Fitness',   'ctl',array(
		'decimals' => 2 ,
		'aggregateFunction' => "AVG"
)
); 


$chart->addSeries('TSB', 'tsb', array(
		'displayType' => "Line",
		'decimals' => 2 ,
		'aggregateFunction' => "AVG"
));

$chart->addTrendLine("DANGER", -30);
$chart->addTrendLine("HARD_TRAINING", -20);

/*

$chart->addSeries('TSS', 'tss', array(
		'displayType' => "Line",
		'decimals' => 2 ,
		'aggregateFunction' => "SUM"
));
;
$chart->addSeries('IF', 'if', array(
		'displayType' => "Line",
		'decimals' => 2 ,
		'aggregateFunction' => "SUM",
'onSecondYAxis' => true
));
*/


Dashboard::addComponent($chart);

Dashboard::Render();
//print_r($data);


?>	
