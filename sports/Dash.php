<html>
  <head>
    <title>RazorFlow Embedded Dashboard</title>
    <script src="../javascripts/jquery-1.11.2.min.js" type="text/javascript"></script>
    <script src="../rf/razorflow.jquery.js" type="text/javascript"></script>

    <script type='text/javascript'>
      $(document).ready(function(){
       $("#dashboardTarget").razorflow({
          url: './dashboardv2.php',
     height: 768, // 800 pixels
   width: 1024 // 600 pixels
        });
      });
    </script>
  </head>
  <body>
    <h2>Perf Management</h2>

 <?php
define('PUN_ROOT', './');
require PUN_ROOT.'config2.php';
require PUN_ROOT.'include/fonctions.php';

if (isset($_POST['user']))
{
	$user=$_POST['user'];
}

if (isset($_POST['fseuil']))
{
	$fseuil=$_POST['fseuil'];
}

if (isset($_POST['tca']))
{
	$tca=$_POST['tca'];
}

if (isset($_POST['tcc']))
{
	$tcc=$_POST['tcc'];
}

if (isset($_POST['atl']))
{
	$atcl=$_POST['atl'];
}

if (isset($_POST['ctl']))
{
	$ctl=$_POST['ctl'];
}

if (isset($_POST['start']))
{
	$start=$_POST['start'];
}

if (isset($_POST['end']))
{
	$end=$_POST['end'];
}
if (isset($_POST['old']))
{
	$old=$_POST['old'];
}





$link=connect_db($db_host, $db_username, $db_password, $db_name);

if ($user &&  $fseuil &&  $tca &&  $tcc &&  $start  &&  $end )
{

    mysqli_query($link,"UPDATE `perf_setting` SET `user`='$old' , `fseuil`='$fseuil',`Tca`='$tca',`Tcc`='$tcc',`atl`='$atl',`ctl`='$ctl',`start`='$start',`end`='$end' WHERE user='$old';");
}




$query_settings = "SELECT * FROM `perf_setting` WHERE user='mathieugravil';";
$result_settings = mysqli_query($link,$query_settings) or die("La requete $query_settings a echouee");
$settings=mysqli_fetch_row ($result_settings );

print"<form action=\"Dash.php\" method=\"post\">";
printf("<input type=\"hidden\" name=\"old\" value=\"%s\"><table >\n
<TR>\n
<TD> username</TD><TD><Input name=\"user\" type=\"text\"   value=\"%s\" size=\"10\"/> </TD></TR>\n
<TR><TD> Fseuil </TD><TD> <Input name=\"fseuil\" type=\"int\"  value=\"%s\" size=\"3\"/></TD></TR>\n 
<TR><TD> Tca(days) </TD><TD> <Input name=\"tca\" type=\"int\"  value=\"%s\" size=\"3\"/></TD></TR>\n
<TR><TD> Tcc (days) </TD><TD> <Input name=\"tcc\" type=\"int\"  value=\"%s\" size=\"3\"/></TD></TR>\n
<TR><TD> Atl_start </TD><TD> <Input name=\"atl\" type=\"int\"  value=\"%s\" size=\"3\"/></TD></TR>\n
<TR><TD> Ctl_start </TD><TD> <Input name=\"ctl\" type=\"int\"  value=\"%s\" size=\"3\"/></TD></TR>\n
<TR><TD> Start </TD><TD> <Input name=\"start\" type=\"char\"  value=\"%s\" size=\"3\"/></TD></TR>\n
<TR><TD> End </TD><TD> <Input name=\"end\" type=\"char\"  value=\"%s\" size=\"3\"/></TD></TR>\n
\n",$settings[0],$settings[0] ,$settings[1],$settings[2],$settings[3],$settings[4],$settings[5],$settings[6],$settings[7]);
print "<TR><TD>&nbsp</TD><TD><INPUT TYPE=\"SUBMIT\" VALUE=\"Report\"/></TR>";

//


mysqli_free_result($result_settings);
mysqli_close($link);
?>
<div id="dashboardTarget"></div>

  </body>
</html>
