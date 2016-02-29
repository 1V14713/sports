<?php
define('PUN_ROOT', '../');
require PUN_ROOT.'sports/config2.php';
require PUN_ROOT.'sports/include/fonctions.php';
require PUN_ROOT.'/rf/razorflow.php';



$now = new DateTime();
$last3months = $now->sub(new DateInterval('P4M'));
$start='1981/06/14';
$end=date("Y/m/d");
$fseuil=150 ;
$datetime1=new DateTime('2012-04-13');
$Tca=7;
$Tcc=42;
$atl_b=0;
$ctl_b=0;


$datetime1=new DateTime($start);


$link=connect_db($db_host, $db_username, $db_password, $db_name);



$query="select seance_id, name, sport_name, date, calories, distance, duration, 
		average/$fseuil  , (100*TIME_TO_SEC(duration)*average*average)/(3600*$fseuil*$fseuil), average, maximum, 
		 Vaverage, Vmaximum , altitude
from seances, sport_type 
Where seances.sport_id = sport_type.sport_id
order by date asc;";

$result = mysql_query($query) or die("La requete  $query a echouee");
$num_rows = mysql_num_rows($result);
$k=1;	

while ($row = mysql_fetch_array($result, MYSQL_NUM))
{
	if ($k == 1 )
	{	
	$datetime1=new DateTime($row[3]);
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
	if($temp > $last3months )
	{
		$data[]=array($temp->format('Y-m-d'),floatval($atl), floatval($ctl),floatval($tsb), 0, 0);
	}
	}

//	$atl= $atl_b+($row[8]-$atl_b)/$Tca;
//$ctl= $ctl_b +($row[8]-$ctl_b)/$Tcc;
$atl= $atl_b +($row[8]- $atl_b)*(1- exp(-1/$Tca));
$ctl= $ctl_b +($row[8] - $ctl_b)*(1-exp (-1/$Tcc));


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

if($current_date > $last3months )
{
		$data[]=array($current_date->format('Y-m-d'),floatval($atl), floatval($ctl),floatval($tsb),floatval($if),floatval($tss));
}



 $k=$k+1;
}

$now =  new DateTime();
	$interval = $datetime1->diff($now);
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


mysql_free_result($result);
mysql_close($link);

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
		'adaptiveYMin' => true,
	'minValue' => -50,
	'maxValue' => 200
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
