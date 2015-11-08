<?php
/* ----------------------------------------------------------- */
// A mettre obligatoirement avant le code html de votre site ! 
require_once 'SudokuSRC/_inc.header.php';
/* ----------------------------------------------------------- */
?>

<!-- Code HTML de votre site -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />  

  <?php 
  /* Cette commande doit être placée entre les balises <head></head> du code html */
  /* ----------------------------------------------------------- */
    require_once 'SudokuSRC/_inc.html-header.php';
  /* ----------------------------------------------------------- */
  ?> 
  
  <meta name="author" content="Nicolas Lion" />
  <title>PHPSUDO</title>
</head>
<body>
    <!-- Variables GET que doit recevoir le script pour afficher une grille : 
    || = ou 
    -------------------------------------------------------------------
    dimension=4*2 || 6*2 || 8*4 || 9*3 || 10*5 || 12*3 || 14*7 || 16*4
    type= || symbole 
    level= 0 || 1 || 2
    --------------------------------------------------------------------
     -->
    <div>
    
    <?php
    /* ----------------------------------------------------------- */
    if(!$refresh) 
       echo $html;
    /* ----------------------------------------------------------- */
    ?>
    
    </div> 

<!-- OPTIONNEL : Pour le wap non terminé ! ne fonctionne pas bien sur les grille superieur à 9*9  -->
<!-- Pour le wap -->
<?php
//on verrifit la resolution

  $largeur_max=$w*$DimensionCase;

   echo '
   <script type="text/javascript">
  //<![CDATA[
    function getElementsByClass()
    {
      input = document.getElementsByTagName("input");
      var resultats = new Array();
      for (i = 0; i < input.length; i++) 
      {
        if (input[i].id.substring(0, 3) == "id_" ) 
          resultats.push(input[i]);
      }
       return resultats;
  	}
  	
  	var elm=getElementsByClass();
  	
  	
  	var h="";
    var w="";
    if (document.all)
    {
    h=document.body.clientHeight-30;
    w=document.body.clientWidth-30;
    }
    else
    {
    w=window.innerWidth-30;
    h=window.innerHeight-30;
    }
    if (screen.width<700)
    {
      	if (h<w)
      	{
      	  new_size=(w/'.$w.');
        }
        else
        {
        	  new_size=(h/'.$w.');
        }
       
        var Font=new_size-10;
        
        for(var i = 0; i < elm.length; i++)
      	 {
            elm[i].style.width=new_size;
            elm[i].style.height=new_size;
            elm[i].style.fontSize=Font+"px";
        }
    }
    //]]>
   </script>';
?>   
<!-- FIN POUR LE WAP -->
</body>
</html>
