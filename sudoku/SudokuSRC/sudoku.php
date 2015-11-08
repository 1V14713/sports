<?php
/*
PHPSUDO 2.0 Licence
Copyright � Nicolas Lion, 17/04/2008
Email : nicolas@lioninformatique.com
Site : http://www.lioninformatique.com

*****************************************************************************************************************************
**                                                                                                                         **
**   Ce logiciel est un programme informatique pour jouer au sudoku. Il g�n�re des grilles � solution unique.              **
**   Ce logiciel est r�gi par la licence CeCILL-B soumise au droit fran�ais et respectant les principes                    **
**   de diffusion des logiciels libres. Vous pouvez utiliser, modifier et/ou redistribuer ce programme                     **
**   sous les conditions de la licence CeCILL-B telle que diffus�e par le CEA, le CNRS et l'INRIA sur                      **
**   le site "http://www.cecill.info".                                                                                     **
**                                                                                                                         **
**   En contrepartie de l'accessibilit� au code source et des droits de copie, de modification et de                       **
**   redistribution accord�s par cette licence, il n'est offert aux utilisateurs qu'une garantie limit�e.                  **
**   Pour les m�mes raisons, seule une responsabilit� restreinte p�se sur l'auteur du programme, le titulaire              **
**   des droits patrimoniaux et les conc�dants successifs.                                                                 **
**                                                                                                                         **
**   A cet �gard l'attention de l'utilisateur est attir�e sur les risques associ�s au chargement, �                        **
**   l'utilisation, � la modification et/ou au d�veloppement et � la reproduction du logiciel par                          ** 
**   l'utilisateur �tant donn� sa sp�cificit� de logiciel libre, qui peut le rendre complexe � manipuler et qui            **
**   le r�serve donc � des d�veloppeurs et des professionnels avertis poss�dant des connaissances informatiques            **
**   approfondies. Les utilisateurs sont donc invit�s � charger et tester l'ad�quation du logiciel � leurs besoins         **
**   dans des conditions permettant d'assurer la s�curit� de leurs syst�mes et ou de leurs donn�es et,                     **
**   plus g�n�ralement, � l'utiliser et l'exploiter dans les m�mes conditions de s�curit�.                                 **
**                                                                                                                         **
**   Le fait que vous puissiez acc�der � cet en-t�te signifie que vous avez pris connaissance de la licence CeCILL-B,      **
**   et que vous en avez accept� les termes.                                                                               **
**                                                                                                                         **
*****************************************************************************************************************************
*/
//on recupere les variable get pour dimensions sudo
if((isset($_GET['h']) && isset ($_GET['w'])) || isset($_GET['dimension']))
{
    if (isset($_GET['dimension']))
    {
      $r=explode('*',$_GET['dimension']);
      $h=$r[1];
      $w=$r[0];
    }
    else
    {
      $h=$_GET['h'];
      $w=$_GET['w'];
    }

   $res=@is_int(($w%$h)) or die('Erreur type pour les dimensions !'); 
   
    if ($w%$h==0 && $w!=$h)
    {

    }
    else
     exit('Erreur, les dimensions ne sont pas correctes!');

    if(isset($_GET['level'])) $level=$_GET['level'];

  //Grille � remplir en ligne

  if (!isset($_POST['valide_x']) && !isset($_POST['lost_x'])) //La grille n'est pas valid�, et le joueur n'a pas demand� la solution
  { 
    $sudo=new SuDoKu(true,$w,$h,$level);

    if (isset($_GET['type']) && $_GET['type']=='symbole')
       $sudo->WithSymbol=true;  

  
    $Grille=$sudo->drawing($sudo->IncompleteGrille);
    $Grille->Assign('ButtonValideStatut',''); //les boutons sont tous affich�s
    $Grille->Assign('ChronoStatut',''); //le chrono est d�marr�
    $Grille->Assign('action',htmlentities($_SERVER['REQUEST_URI'])); // action du formulaire
    $_SESSION['GrilleSolution']=$sudo->GrillePleine;
    $_SESSION['GrilleIncomplete']=$sudo->IncompleteGrille;
     
    if ($sudo->ValidIncompleteGrille || !$sudo->TimeOut)
    {
      if(!isset($refresh))
      {
        $refresh=false;
        $html=$Grille->PrintPage('Grille.tpl');
      }
    }
    else
      //echec calcul grille sudoku
      $refresh=true; //on doit refraichir la grille
  }
  else //demande de correction et ou solution
  {
      $Winner=false; //pas gagnant
      //temps d'execution
      $_SESSION['Timing']=$_POST['timing'];
      $StatutButton='';
      $sudo=new SuDoKu(false,$w,$h); // false pour ne pas reg�n�rer une grille de sudoku mais pour utiliser une existante stoqu� dans la session
      
      if (isset($_GET['type']) && $_GET['type']=='symbole')
       $sudo->WithSymbol=true;
       
      $sudo->GrillePleine=$_SESSION['GrilleSolution'];
      $sudo->IncompleteGrille=$_SESSION['GrilleIncomplete'];
      $nbr_erreurs=$sudo->Correction($_POST);
      
      //on doit r�afficher la grille;
      $Grille=$sudo->drawing($sudo->IncompleteGrille);
      
      if ($nbr_erreurs>0)
      {
        $tpl->assign('NbrErreurs',$nbr_erreurs);
        $html=$tpl->PrintPage('Perdu.tpl'); 
        $Winner=false; 
      }
      else
      {
         // Le joueur � gagn� !
         $html=$tpl->PrintPage('Gagne.tpl'); 
         $StatutButton='display:none;';
         $Chronostatut='StopChrono();';
         //on enregistre le score ?
         $Winner=true;
      }
      
      if (isset($_POST['lost_x']))
      { 
        //le joueur abandonne et demande la solution
        $Grille2=$sudo->drawing($sudo->GrillePleine,'GrilleSolution.tpl');
        $html.=$Grille2->PrintPage('GrilleSolution.tpl'); 
        $Chronostatut='StopChrono();';
        $StatutButton='display:none;';
        unset($Grille2);
      }
      else
      {
        $Grille->Assign('ButtonValideStatut','');  
      }
      $Grille->Assign('ButtonValideStatut',$StatutButton);
      $Grille->Assign('action',$_SERVER['REQUEST_URI']);
      $Grille->Assign('ChronoStatut',$Chronostatut);
      $html.=$Grille->PrintPage('Grille.tpl');
      
  } 
  
  unset($sudo,$Grille);
}
?>
