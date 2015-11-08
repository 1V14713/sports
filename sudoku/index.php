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
  <title>PHPSUDO Sudoku</title>
</head>
<body>
<div id="body">

<div style="margin:auto;text-align:center;margin-bottom:30px;">
    <form method='get' action='.'>
    <select name='dimension' style="font-family:arial;font-size:11px;">
      <option value='4*2'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='4*2') echo '  selected="selected"'; ?>>4 X 4</option>
      <option value='6*2'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='6*2') echo '  selected="selected"'; ?>>6 X 6</option>
      <option value='8*4'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='8*4') echo '  selected="selected"'; ?>>8 X 8</option>
      <option value='9*3'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='9*3') echo '  selected="selected"'; ?>>9 X 9</option>
      <option value='10*5'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='10*5') echo ' selected="selected"'; ?>>10 X 10</option>
      <option value='12*3'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='12*3') echo ' selected="selected"'; ?>>12 X 12</option>
      <option value='14*7'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='14*7') echo '  selected="selected"'; ?>>14 X 14</option>
      <option value='16*4'<?php if (isset($_GET['dimension']) && $_GET['dimension']=='16*4') echo '  selected="selected"'; ?>>16 X 16</option>
    </select>
    <select name="type" style="font-family:arial;font-size:11px;">
      <option value="">Numéros</option>
      <option value="symbole"<?php if (isset($_GET['type']) && $_GET['type']=='symbole') echo '  selected="selected"'; ?>>Lettres</option>
    </select>
    <select name="level" style="font-family:arial;font-size:11px;">
      <option value="0">Facile</option>
      <option value="1"<?php if (isset($_GET['level']) && $_GET['level']=='1') echo '  selected="selected"'; ?>>Moyen</option>
      <option value="2"<?php if (isset($_GET['level']) && $_GET['level']=='2') echo '  selected="selected"'; ?>>Difficile</option>
    </select>
    <input type="submit" value="ok" style="font-family:arial;font-size:11px;" />
    </form>
</div>
    <div>
    
    <?php
    /* ----------------------------------------------------------- */
    if(!$refresh) // si refresh=false pas de timeout
       echo $html;
    /* ----------------------------------------------------------- */
    ?>
    
    </div> 
</div>

<!-- Pour le wap 2 -->

</body>
</html>
