<?php
  session_start();
  header('Content-type: application/javascript');

  if (isset($_SESSION['Timing']))
  {
    //La grille a été validé -> on continu le timing 
    //Le timing s'arrêtera si le joueur a terminé la grille sans ereur
    //On convertit les centiemes en secondes et en minutes
    echo 'var CentTotal='.$_SESSION['Timing'].';';
    echo 'var min='.(int)($_SESSION['Timing']/600).';';
    echo 'var sec='.(int)(($_SESSION['Timing']%600)/10).';';
    echo 'var cent='.(int)(($_SESSION['Timing']%600)%10).';';
    unset($_SESSION['Timing']); // cette variable n'est plus utile
  }
  else
  {
    echo '
    var cent=0;
    var CentTotal=0;
    var sec=0;
    var min=0; 
    '; 
  }
?>
var GetTime=1;
function chrono()
{
  cent++; //incrémentation des dixièmes de 1
  CentTotal++;
  if (cent>9)
  {
        cent=0;
        sec++;
  } 
  
  if (sec>59)
  {
      sec=0;
      min++;
  } 
  document.getElementById('timing').value=CentTotal;
  
  document.getElementById('cents').innerHTML=cent ;
  document.getElementById('secs').innerHTML=sec ;
  document.getElementById('mins').innerHTML=min ;
  if (GetTime==1)
  {
    setTimeout("chrono()",100) ;
  }
  
}
function StopChrono()
{
  GetTime=0;
}

