<HTML>
   <HEAD>
         <meta http-equiv="content-type" content="text/html; charset=iso-8859-1">
	<TITLE>Liste seances</TITLE>
          <link rel="stylesheet" href="include/style.css" type="text/css">
 <style type="text/css" src=style.css></style>
<script type="text/javascript" src="../javascripts/jquery-1.11.2.min.js"></script> 	
<script type="text/javascript" src="../javascripts/jQuery_table_sorter/jquery.tablesorter.js"></script>
 
   </HEAD>
 <BODY>
<script type="text/javascript" >


	  $(document).ready(function() 
    { 
        $("#liste").tablesorter({sortList: [[2,1]]}); 
         $("#Annee").tablesorter();
       $("#Sport_name").tablesorter();
 
    } 
); 
     </script> 	

 

<?php
define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';
require PUN_ROOT.'include/ExportExcel.php';

$start='1981-06-14';
$end=date("Y-m-d");
$fseuil=150 ;
// $datetime1=new DateTime('1981-06-14');
$Tca=7;
$Tcc=42;
$atl_b=0;
$ctl_b=0;

if (isset($_POST['start']))
{
	if ($_POST['start'] != '')
	{
		$start=$_POST['start'];
	}
	else{
		$start='1981-06-14';
		// $start=date('Y/m/d', mktime(0,0,0,date('m'),01,date('Y')));
	}
}

if (isset($_POST['end']))
{
	if ($_POST['end'] != '')
	{
		$end=$_POST['end'];
	}
	else{
		$end=date("Y-m-d");
	}
}


$link=connectsqli_db($db_host, $db_username, $db_password, $db_name);
$query_settings = "SELECT * FROM `perf_setting` WHERE user='mathieugravil'";
$result_settings = mysqli_query($link, $query_settings) or die("La requete $query_settings a echouee");
$settings=mysqli_fetch_array($result_settings, MYSQLI_NUM );

$fseuil=$settings[1] ;
$Tca=$settings[2];
$Tcc=$settings[3];
$atl_b=$settings[4];
$ctl_b=$settings[5];


$query_sports = "select sport_id , sport_name from sport_type order by sport_id desc ";
$sports = array();	
$result_sports = mysqli_query($link , $query_sports) or die("La requete $query_sports a echouee");
if(isset($_POST['sport']) && !empty($_POST['sport'])){
	$sports=$_POST['sport'];
	$all_selected=0;
}
else  {
	while ($row_sport=mysqli_fetch_array($result_sports, MYSQLI_NUM) )
	{
		$sports[] = $row_sport[0];
	}
	print "Vous devez selectionnez au moins un sport !!!!<br>";
$all_selected = 1;
}


$list_sports = "'". implode("', '", $sports) ."'";

$query="select seance_id, name, sport_name, date, calories, distance, duration, 
		format(average/$fseuil,2)  , format((100*TIME_TO_SEC(duration)*average*average/$fseuil)/(3600*$fseuil),2), average, maximum, 
		 Vaverage, Vmaximum , altitude, TIME_TO_SEC(duration)
from seances, sport_type 
Where seances.sport_id = sport_type.sport_id
AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
order by date asc";


echo "<H1>Liste sur la periode du $start au  $end </H1>" ;

print"<form action=\"Listev3.php\" method=\"post\">";
printf("<table border=2>\n
<TR>\n
<TD> First day </TD>\n<TD><Input name=\"start\" type=\"date\"   value=\"%s\" size=\"8\"/> </TD>\n<TD> Last day </TD>\n
 		<TD> <Input name=\"end\" type=\"date\"  value=\"%s\" size=\"8\"/></TD>\n 
</TR>\n",$start ,$end);
print "<TR><TD>Selectione un ou plusieurs sports : </TD><TD><select name=\"sport[]\" multiple>";

$result_sports = mysqli_query($link, $query_sports) or die("La requete $query_sports a echouee");

while ($row_sports = mysqli_fetch_array($result_sports, MYSQLI_NUM))
{
	if ($all_selected == 0)
	{
	if (  in_array($row_sports[0], $sports) )
	{
 printf("<option  value=%s selected>%s </option>",$row_sports[0],  $row_sports[1] );
	}
	else 
	{
		printf(	"<option  value=%s> %s </option>",$row_sports[0], $row_sports[1] );
	}
	}
	else 
	{
		printf("<option  value=%s selected> %s </option>",  $row_sports[0], $row_sports[1] );
	}
}	
print "</select></TD>";	
print "	<TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Report\"/></form></TD><TD><form action=\"Export.php\" method=\"post\">
<input type=\"hidden\" name=\"start\" value=\"$start\">
<input type=\"hidden\" name=\"end\" value=\"$end\">";

$result_sports = mysqli_query($link, $query_sports) or die("La requete $query_sports a echouee");
while ($row_sports = mysqli_fetch_array($result_sports, MYSQLI_NUM))
{
	if ($all_selected == 0)
	{
	if (  in_array($row_sports[0], $sports) )
	{
 printf("<input type=\"hidden\" name=\"sport[]\" value=\"%s\" >",$row_sports[0]);
	}	
	}
	else 
	{
		printf("<input type=\"hidden\" name=\"sport[]\" value=\"%s\" >",$row_sports[0]);
	}
}	

print"
<INPUT TYPE=\"SUBMIT\" VALUE=\"CSV_export\"/></form>
</TD>
</TR>	
</table>\n";

$querysum1 = "select sport_name, SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passe\" ,
sum(calories) as \"Calories depensees\" ,
sum(distance) /1000 as \"distance(km)\" ,
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\",
count(distinct(date)) as \"nb days\" , 
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\"
 
FROM seances, sport_type
WHERE seances.sport_id=sport_type.sport_id
AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
GROUP BY sport_name";

$header[0]="Sport_name";
$header[1]="Duree (HH:MM:ss)";
$header[2]="Calories (cal)";
$header[3]="Distance (km)";
$header[4]="Calorie/heure";
$header[5]= "Nb jours act";
$header[6]="Calorie/ jour act"; 
$header[7]="Distance par jour act (km/j)";
$header[8]="Duree/jour act ";

mysqli_to_html_table($link, $querysum1, $header) ;


$querysum2="SELECT count(distinct(date)) as \"nb days\" ,
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\",
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",
datediff(max(date), min(date)) as \"nb days eff\" ,
format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\",
format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\",
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
from seances
WHERE seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
AND seances.sport_id IN ($list_sports )
";

$header2[0]="NB jours act periode"; //
$header2[1]="Calorie/ jour act"; //
$header2[2]="Distance par jour act";//Nb jour periode";
$header2[3]="Duree/jour act";//Calorie/heure";
$header2[4]="NB jours  periode";
$header2[5]="Calorie/ jour"; //
$header2[6]="Distance par jour (km/j)";//Nb jour periode";
$header2[7]="Duree/jour";//Calorie/heure"; 
mysqli_to_html_table($link, $querysum2, $header2) ;


$querysum3="select date_format(date, '%Y'),  date_format(date, '%M'), 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))) as \"temps passe\" ,
sum(calories) as \"Calories depensees\" , 
sum(distance) /1000 as \"distance(km)\" ,
format(3600*sum(calories)/sum(TIME_TO_SEC(duration)) , 2) as \"Calorie/heure\" ,
count(distinct(date)) as \"nb days\" , 
format(sum(calories)/count(distinct(date)),2) as \"Cal/day\",
format(sum(distance) /1000/count(distinct(date)),2) as \"km/day\", 
SEC_TO_TIME(sum(TIME_TO_SEC(duration))/count(distinct(date)))as \"duration/day\",  
		format(sum(calories)/datediff(max(date), min(date)),2) as \"Cal/dayeff\", 
		format(sum(distance) /1000/datediff(max(date), min(date)),2) as \"km/dayeff\", 
		SEC_TO_TIME(sum(TIME_TO_SEC(duration))/datediff(max(date), min(date)))as \"duration/dayeff\"
		FROM seances, sport_type 
		WHERE seances.sport_id=sport_type.sport_id 
		AND seances.date <= date_format('$end','%Y/%m/%d')
AND seances.date >= date_format('$start','%Y/%m/%d')
		 AND seances.sport_id IN ($list_sports )
GROUP BY  date_format(date, '%Y'),  date_format(date, '%M')
ORDER BY date ";

$header3[0]="Annee";
$header3[1]="Mois"; 
$header3[2]="Duree"; 
$header3[3]="Calorie";
$header3[4]="Distance";
$header3[5]="Calorie/h";
$header3[6]= "Nb jours act";
$header3[7]="Calorie/ jour act"; 
$header3[8]="Distance par jour act (km/j)";
$header3[9]="Duree/jour act ";
$header3[10]="Calorie/ jour";
$header3[11]="Distance par jour (km/j)";
$header3[12]="Duree/jour";
mysqli_to_html_table($link, $querysum3, $header3) ;





print "
<div id=\"users\">
  <input class=\"search\" placeholder=\"Search\" />




<TABLE border=2 id=\"liste\"class=\"tablesorter\">
<thead>
<th>name</th><th>sport</th><th>date</th><th>cal</th><th>dist</th><th>deniv</th><th>duration</th><th> 
		IF</th><th>TSS</th><th>Fmoy</th><th>Fmax</th><th>Vaverage</th><th>Vmaximum </th><th>ATL</th><th>CTL</th><th>TSB</th>
		<th>ACTION</th>
		</TR></thead>
<tbody class=\"list\">";

$result = mysqli_query($link, $query) or die("La requete  $query a echouee");
$num_rows = mysqli_num_rows($result);
echo "$num_rows Rows\n";
		
$k=1;	

while ($row = mysqli_fetch_array($result, MYSQLI_NUM))
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
		$atl= $atl_b - $atl_b/$Tca;
		$ctl= $ctl_b - $ctl_b/$Tcc;
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

$if =  $fmoy_day/$fseuil  ;
$tss = (100*$total_duration_sec*$fmoy_day*$fmoy_day/$fseuil)/(3600*$fseuil);

$atl= $atl_b+($tss-$atl_b)/$Tca;
$ctl= $ctl_b +($tss-$ctl_b)/$Tcc;
$tsb = number_format($ctl - $atl, 2) ;
$atl_b=$atl ;
$ctl_b=$ctl;
$atl2=number_format($atl, 2);
$ctl2=number_format($ctl, 2);
$datetime1=$current_date;

#$tss= number_format($row[8], 2);
#$if=number_format($row[7], 2);
$temp2=$current_date->format('Y-m-d') ;

//print"<form action=\"Voir.php\" method=\"post\">";
//printf("<input type=\"hidden\" name=\"seance_id\" value=\"%s\">",$row[0]);
$toto=str_replace(' ','_',$row[2]);
echo"<TR id=\"$toto\">
<TD class=\"name\">$row[1]</TD><TD class=\"sport\">$row[2]</TD><TD class=\"date\">$row[3]</TD><TD class=\"cal\">$row[4]</TD><TD class=\"dist\">$row[5]</TD><TD class=\"deniv\">$row[13]</TD><TD class=\"duration\">$row[6]</TD><TD class=\"IF\">
$if</TD><TD class=\"TSS\">$tss</TD><TD class=\"Fmoy\">$row[9]</TD><TD class=\"Fmax\">$row[10]</TD><TD class=\"Vaverage\">$row[11]</TD><TD class=\"Vmaximum\">$row[12]</TD>
<TD class=\"ATL\">$atl2</TD><TD class=\"CTL\">$ctl2</TD><TD class=\"TSB\">$tsb</TD>
<TD><form action=\"Voir.php\" method=\"post\"><input type=\"hidden\" name=\"seance_id\" value=\"$row[0]\"><INPUT TYPE=\"SUBMIT\" VALUE=\"Voir  \"/></form></TD>

</TR>" ;
//print"</form>";
 $k=$k+1;
}
print "</tbody></TABLE></div>";

mysqli_free_result($result);
mysqli_free_result($result_sports);
mysqli_close($link);
?>

<script type="text/javascript" src="../javascripts/list.min.js"></script> 
<script type="text/javascript" >
var options = {
  valueNames: [ 'name', 'date', 'sport',  ]
};

var userList = new List('users', options);



     </script> 	
	   </BODY>
 </HTML>
	
