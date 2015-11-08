<?php
 header("Content-type: text/css");
 
 include '_inc.config.php';
 
 $TailleTexte=$DimensionCase-6;

?>

.GrilleCSS
{
  
  margin:0;
  margin-right:-4px;
  height:<?php echo $DimensionCase; ?>px;
  width:<?php echo $DimensionCase; ?>px;
  font-family:<?php echo $FontFamily; ?>;
  vertical-align:middle;
  text-align:center;
  font-size:<?php echo $TailleTexte; ?>px;
  color:<?php echo $CouleurChiffres; ?>;
}

.GrilleCSS[disabled] {color:#333!important;}

.TopCase
{
  border-top:2px solid <?php echo $CouleurGrille; ?>;
}
.BottomCase
{
  border-bottom:2px solid <?php echo $CouleurGrille; ?>;
}
.RightCase
{
  border-right:2px solid <?php echo $CouleurGrille; ?>;
}
.LeftCase
{
  border-left:2px solid <?php echo $CouleurGrille; ?>;
}

.BorderLeft
{
  border-left:1px solid <?php echo $CouleurGrille; ?>;
}
.BorderTop
{
  border-top:1px solid <?php echo $CouleurGrille; ?>;
}

.BorderBottomNull
{
  border-bottom:0px;
}
.BorderRightNull
{
  border-right:0px;
}

.Disabled
{
  color:#999;
  background: <?php echo $CouleurCaseSolution; ?>;
  cursor:default;
}

.AlignGrille
{
  margin-left:auto;
 	margin-right:auto;
  text-align:center;
  font-family:<?php echo $FontFamily; ?>;

}
