<?php
class template 
{
	var $TemplateFolder;
	var $Container;
	var $key;
	var $blocs=array();
	var $ValueVar;
	var $mode='debug';
  var $buffer=FALSE; //public
  var $BufferFolder;

	function template($folder,$CacheFolder='.cache/') 
  {
		$this->TemplateFolder = $folder;
		$this->BufferFolder = $CacheFolder;
		$this->Container = "";
		$this->Key = array();
		$this->ValueVar = array();
	}

	function AddTemplate($file) 
  {
    // mis en cache activé ?
    if ($this->buffer)
    {
      //on verifit que le dossier existe
      if (!is_dir($this->BufferFolder))
      {
        //création du dossier 
        mkdir($this->BufferFolder);
        chmod($this->BufferFolder,'0777');
      }
    }
		 //on vérifie que le fichier template existe
     if(is_file($this->TemplateFolder.$file) || !is_file($this->BufferFolder.$file))
     { 
        $fp=fopen($this->TemplateFolder.$file,'r');
        $this->Container = fread($fp,filesize($this->TemplateFolder.$file));
        fclose($fp);
	   }
	   else if($this->mode=='debug')
     {
        echo 'Template introuvable';
        $this->clear();
    } 
  }

	function assign($cle, $valeur) 
  {
		$cle = $this->ajouteAccolade($cle);
		array_push($this->Key, $cle);
		array_push($this->ValueVar, $valeur);
	}

	function ajouteAccolade($val) 
  {
		return "[".$val."]";
	}

	function PrintPage($file) 
  {
    if ($this->Container=='')
    {
		  $this->AddTemplate($file);
		} //sion le fichier est déjà chargé
		
		$fin_bloc=array_keys($this->blocs);
    $i=0;
    //La boucle est éxécutée tant qu'il reste des clees à traiter dans l'array
    while(array_key_exists($i, $fin_bloc))
    {
        $j=$i-1;
        //Si $sous_blocs est à true et qu'il existe un sous-bloc, on le traite
        if($j>-1 and $sous_blocs=TRUE)
        {
             $this->blocs[$fin_bloc[$i]]=
             preg_replace('!<\!--'.$fin_bloc[$j].'-->(.+)<\!--/'.$fin_bloc[$j].'-->!isU',
             $this->blocs[$fin_bloc[$j]], $this->blocs[$fin_bloc[$i]]);
        } 
        
       //On traite le bloc lui-même
      $this->Container=preg_replace('!<\!--'.$fin_bloc[$i].'-->(.+)<\!--/'.$fin_bloc[$i].'-->!isU',$this->blocs[$fin_bloc[$i]], $this->Container);
      //$this->Container = str_replace($fin_bloc[$i]), array_values($this->var), $this->Container);
      
      //on supprime tous les commentaires !
		  $this->Container=str_replace('<!--'.$fin_bloc[$i].'-->','', $this->Container);
      $this->Container=str_replace('<!--/'.$fin_bloc[$i].'-->','', $this->Container);
      $i++;
    } 
    
    $this->Container = str_replace($this->Key, $this->ValueVar, $this->Container);
		
    if ($this->buffer)
    {
        //creation du fichier buffer
        $fp=fopen($this->BufferFolder.'.'.$file,'w+');
        fwrite($fp,$this->Container);
        fclose($fp); 
    
    }
    $res=$this->Container;
    $this->Container="";
    $this->PrintPage = "";
		$this->Key = array();
		$this->ValueVar = array();   
		return $res;
	}
	
	function setTemplateFolder($folder) 
  {
		$this->TemplateFolder = folder;
	}
	
	function ExistsFileBuffer($file)
	{
      if (is_file($this->BufferFolder.'.'.$file))
      {
        $fp=fopen($this->BufferFolder.'.'.$file,'r');
        $cache=fread($fp,filesize($this->BufferFolder.'.'.$file));
        fclose($fp);
        return $cache;
      }
      else
      {
        return FALSE;
      }
  }
  
  
  function bloc($bloc,$array,$file,$IFBloc=false)
  {
        $this->AddTemplate($file);  
        //On vérifie que le bloc existe dans le fichier template
        if(preg_match('<!--'.$bloc.'-->', $this->Container) and preg_match('<!--/'.$bloc.'-->', $this->Container))
        { 
            //on définit $contenu_bloc comme le contenu du bloc
            ereg('<!--'.$bloc.'-->(.*)<!--/'.$bloc.'-->', $this->Container, $contenu_bloc_tableau);
            $contenu_bloc=$contenu_bloc_tableau[0];
            $i=1;
            /*on traite toutes les clefs et les valeurs de $array pour les mettre dans
            deux tableaux associatifs distincts*/
            while(list($key, $val) = each($array))
            {
                //on vérifie à chaque foi que la variable se trouve bien dans dans l'array
                if(preg_match($this->ajouteAccolade($key), $this->Container))
                {
                  $cle[$i]=$this->ajouteAccolade($key);
                  $valeur[$i]=$val;
                  $i++;
                }
             }
            //On remplace toutes les variables du bloc par leur contenu
            $bloc_final=str_replace($cle, $valeur, $contenu_bloc);
            
            
            if (isset($this->blocs[$bloc]))
            {
                  //Si le bloc existe, on insère la partie qu'on vient de traiter
                  $this->blocs[$bloc].=$bloc_final;
                  
            }
            else
            {
                //Sinon, on en créé un nouveau
                $this->blocs[$bloc]=$bloc_final;
                
            }
            
      }
      else if ($this->mode=='debug')
      {
             echo 'ERREUR bloc. Le bloc "'.$bloc.'" n\'existe pas sur le fichier template : '.$file;
             die();
      }
 } 
	
	function DropCach($file)
	{
	   if (is_file($this->BufferFolder.'.'.$file))
	   {
      unlink($this->BufferFolder.'.'.$file);
     }
  }
	
  function clear()
  {
      unset($this->TemplateFolder,$this->Container,$this->Key,$this->ValueVar);
  }
}

?>
