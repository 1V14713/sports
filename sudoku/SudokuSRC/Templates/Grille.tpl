<div class='AlignGrille'>
  <div id='SudokuGrille'>
  <!-- Affichage Chrono -->
  <div>
    <span id='mins'>0</span>' <span id='secs'>0</span>'' <span id='cents' style="display:none;">0</span>
  </div>
  
  <form action='[action]' method='post' style='boder:0;margin:0;margin-top:15px;'> 
    
    <!-- Chrono -->
    <p>
    <input type='hidden' id='timing' name='timing' value='' />
    </p>
    <script type="text/javascript">chrono();[ChronoStatut]</script>
    
    <!--Cases-->
      [DIV]
        <input type='text' name='case_[coord]' id='id_[coord]' value='[value]' class='GrilleCSS [class]' [Disabled] />
      [/DIV]  
    <!--/Cases-->

    <input type='image' name='lost'  src='SudokuSRC/imgs/solution.png' value='1' style='margin-top:15px;border:0px;[ButtonValideStatut]' onclick="javascript:StopChrono();" title='Solution' />
    <input type='image' name='valide' src='SudokuSRC/imgs/valid.png' value='1' style='margin-left:10px;margin-top:15px;border:0px;[ButtonValideStatut]' onclick="javascript:StopChrono();" title='Valider la grille' />
  
  </form>
  </div>
  
<!-- 
Pour respecter mon travail et la licence vous devez laisse le copyright sur l'application
-->
<div style="font-family:arial;font-size:9px;margin-top:30px;font-weight:bold;color:#ccc;">
Copyright © Nicolas Lion, 2008 <br /> <a href="http://stats.lioninformatique.com/phpmyvisites.php?url=http%3A//nlion.free.fr/sudoku/versions/sudoku_PHP-1.9.zip&amp;id=1&amp;pagename=FILE:Sudoku.1.9.zip" style="color:#ccc;" target="_blank">PHPSUDO</a> est sous <a href="http://www.cecill.info/licences/Licence_CeCILL-B_V1-fr.html" target="_blank" style="color:#ccc;text-decoration:none;">licence CECILL-B </a><br /> <a href="http://www.lioninformatique.com" target="_blank" style="color:#ccc;">LionInformatique.com</a>

</div>  
</div>
<div style="display:none;">
  <!-- Ne pas effacer / Pour ameliorer le PR de mon site. Ce texte n'est pas visible sur votre site. Merci !-->
  <a href="http://www.lioninformatique.com">LionInformatique PHPSUDO</a>
  <a href="http://www.sudoku.lioninformatique.com/">PHPSUDO WebSite</a>
  <!-- Merci -->
</div>

