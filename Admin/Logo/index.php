<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Now=time();

$chemin = $_FILES['photo']['name'];
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');
$ext_origin = strchr($chemin, '.');

if ((isset($_POST['Enregistrer']))&&(in_array($ext_origin, $ext))) {

// Upload d'image
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/footer";
$REP = $_SERVER['DOCUMENT_ROOT']."/lib/logo/footer";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/footer/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/logo/footerType.png";

    if (!file_exists($rep)) {
        mkdir($rep, 0777);
    }

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleLargeur_photo = 190;
      $NouvelleHauteur_photo = ( ($TailleImageChoisie[1] * (($NouvelleLargeur_photo)/$TailleImageChoisie[0])) );     


  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='1'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($REP.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if (isset($_POST['Reset'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!=$Default) {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='1'");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !";
    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
}

if ((isset($_POST['Enregistrer2']))&&(in_array($ext_origin, $ext))) {

// Upload d'image
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/header/";
$REP = $Home."/lib/logo/header/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/header/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/logo/headerType.png";

    if (!file_exists($rep)) {
        mkdir($rep, 0777);
    }

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
        ErreurLog($Erreur);
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleHauteur_photo = 95;
      $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );   

  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='2'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($REP.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if (isset($_POST['Reset2'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!=$Default) {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='2'");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !";
    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid)); 
}

if ((isset($_POST['Enregistrer3']))&&(in_array($ext_origin, $ext))) {

// Upload d'image
$rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/nav/";
$REP = $Home."/lib/logo/nav/";
$fichier = basename($chemin);
$taille_origin = filesize($_FILES['photo']['tmp_name']);
$hash = md5(uniqid(rand(), true));
$Chemin_upload = $Home."/lib/logo/nav/".$hash.$fichier."";
$TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
$taille_max = 10000000;
$Default=$Home."/lib/logo/navType.png";

    if (!file_exists($rep)) {
        mkdir($rep, 0777);
    }

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
        ErreurLog($Erreur);
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleHauteur_photo = 80;
      $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );   

  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='3'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($REP.basename($Param->logo));
                }
                
                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='3'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }   
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
    if (in_array($ext_origin, $ext2)) {   
        $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagealphablending($NouvelleImage_photo, false);
        imagesavealpha($NouvelleImage_photo, true);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){

                $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='3'");
                $SelectParam->execute();
                $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

                if($Param->logo!=$Default) {
                  unlink($rep.basename($Param->logo));
                }

                $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='3'");
                $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                $Insertlogo->execute();

                $Valid="Logo ajouté avec succès !";
                header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if (isset($_POST['Reset3'])) {
  
    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='3'");
    $SelectParam->execute();
    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);

    if($Param->logo!=$Default) {
      unlink($rep.basename($Param->logo));
    }
    
    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='3'");
    $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
    $Insertlogo->execute();

    $Valid="Logo ajouté avec succès !";
    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid)); 
}

if ((isset($_POST['Enregistrer4']))&&(in_array($ext_origin, $ext))) {
    
    // Upload d'image
    $rep = $_SERVER['DOCUMENT_ROOT']."/lib/logo/region/";
    $REP = $Home."/lib/logo/region/";
    $fichier = basename($chemin);
    $taille_origin = filesize($_FILES['photo']['tmp_name']);
    $hash = md5(uniqid(rand(), true));
    $Chemin_upload = $Home."/lib/logo/region/".$hash.$fichier."";
    $TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
    $taille_max = 10000000;
    $Default=$Home."/lib/logo/regionType.png";
    
        if (!file_exists($rep)) {
            mkdir($rep, 0777);
        }
    
        if($taille_origin>$taille_max){
            $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 2800px sur 1800px de hauteur";
            ErreurLog($Erreur);
        }
        if (!isset($Erreur)){       
          //si largeur + grande
    
          $NouvelleHauteur_photo = 38;
          $NouvelleLargeur_photo = ( ($TailleImageChoisie[0] * (($NouvelleHauteur_photo)/$TailleImageChoisie[1])) );   
    
      if (in_array($ext_origin, $ext1)) {
     
            $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
            $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
            imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);
    
            if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){
    
                    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE WHERE id='4'");
                    $SelectParam->execute();
                    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);
    
                    if($Param->logo!=$Default) {
                      unlink($REP.basename($Param->logo));
                    }
                    
                    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='4'");
                    $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                    $Insertlogo->execute();
    
                    $Valid="Logo ajouté avec succès !";
                    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
            }   
            else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
        }
        if (in_array($ext_origin, $ext2)) {   
            $ImageChoisie_photo = imagecreatefrompng($_FILES['photo']['tmp_name']);
            $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
            imagealphablending($NouvelleImage_photo, false);
            imagesavealpha($NouvelleImage_photo, true);
            imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);
    
            if (imagepng($NouvelleImage_photo , $rep.$hash.$fichier, 0)){
    
                    $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='4'");
                    $SelectParam->execute();
                    $Param=$SelectParam->fetch(PDO::FETCH_OBJ);
    
                    if($Param->logo!=$Default) {
                      unlink($rep.basename($Param->logo));
                    }
    
                    $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='4'");
                    $Insertlogo->bindParam(':photo', $Chemin_upload, PDO::PARAM_STR);
                    $Insertlogo->execute();
    
                    $Valid="Logo ajouté avec succès !";
                    header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid));
            }
        else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
        }
      }
    }
    
    if (isset($_POST['Reset4'])) {
      
        $SelectParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='4'");
        $SelectParam->execute();
        $Param=$SelectParam->fetch(PDO::FETCH_OBJ);
    
        if($Param->logo!=$Default) {
          unlink($rep.basename($Param->logo));
        }
        
        $Insertlogo=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET logo=:photo WHERE id='4'");
        $Insertlogo->bindParam(':photo', $Default, PDO::PARAM_STR);
        $Insertlogo->execute();
    
        $Valid="Logo ajouté avec succès !";
        header("location:".$Home."/Admin/Logo/?valid=".urlencode($Valid)); 
    }

?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>
<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<script>
	function createInstance() {
        var req = null;
		if (window.XMLHttpRequest)
		{
 			req = new XMLHttpRequest();
		} 
		else if (window.ActiveXObject) 
		{
			try {
				req = new ActiveXObject("Msxml2.XMLHTTP");
			} catch (e)
			{
				try {
					req = new ActiveXObject("Microsoft.XMLHTTP");
				} catch (e) 
				{
					alert("XHR not created");
				}
			}
	    }
        return req;
	};

    function submitForm1(element) { 
        function storing(data) {
        //envoi des element receptionne dans la div
            var element = document.getElementById('Affichage1');
            element.innerHTML = data;
        } 
        
        var req =  createInstance();
        //recuperation des champs du formulaire
        var lien = document.Form_1.lien.value;
        //creation >> nomChamp = nomVariable & nomChamp = nomVariable etc...
        var data = "lien=" + lien;

        req.onreadystatechange = function() { 
            if(req.readyState == 4) {
                if(req.status == 200) {
                    storing(req.responseText);  
                }   
                else {
                    alert("Error: returned status code " + req.status + " " + req.statusText);
                }   
            } 
        }; 
        
        req.open("POST", "/Admin/Logo/modifLien.php?id=<?php echo $ParamLogoHeader->id; ?>", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //envoi
        req.send(data);  
    }

    function submitForm2(element) { 
        function storing(data) {
        //envoi des element receptionne dans la div
            var element = document.getElementById('Affichage2');
            element.innerHTML = data;
        } 
        
        var req =  createInstance();
        //recuperation des champs du formulaire
        var lien = document.Form_2.lien.value;
        //creation >> nomChamp = nomVariable & nomChamp = nomVariable etc...
        var data = "lien=" + lien;

        req.onreadystatechange = function() { 
            if(req.readyState == 4)
            {
                if(req.status == 200)
                {
                    storing(req.responseText);  
                }   
                else    
                {
                    alert("Error: returned status code " + req.status + " " + req.statusText);
                }   
            } 
        }; 
        
        req.open("POST", "/Admin/Logo/modifLien.php?id=<?php echo $ParamLogoFooter->id; ?>", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //envoi
        req.send(data);  
    }

    function submitForm3(element) { 
        function storing(data) {
        //envoi des element receptionne dans la div
            var element = document.getElementById('Affichage3');
            element.innerHTML = data;
        } 
        
        var req =  createInstance();
        //recuperation des champs du formulaire
        var lien = document.Form_3.lien.value;
        //creation >> nomChamp = nomVariable & nomChamp = nomVariable etc...
        var data = "lien=" + lien;

        req.onreadystatechange = function() { 
            if(req.readyState == 4)
            {
                if(req.status == 200)
                {
                    storing(req.responseText);  
                }   
                else    
                {
                    alert("Error: returned status code " + req.status + " " + req.statusText);
                }   
            } 
        }; 
        
        req.open("POST", "/Admin/Logo/modifLien.php?id=<?php echo $ParamLogoNav->id; ?>", true);
        req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        //envoi
        req.send(data);  
    }
</script>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<H1>En-tête</H1>
<form name="Form_1" action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoHeader->logo; ?>"/><BR />
<input type="text" name="lien" value="<?php echo $ParamLogoHeader->lien; ?>" placeholder="Lien au clic" onChange="submitForm1()" /><div id="Affichage1"></div><BR /><BR />

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer2" value="Enregistrer"/>
<input type="submit" name="Reset2" value="Réinitialisé"/>
</form>

<H1>En-tête Droite</H1>
<form name="Form_1" action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoRegion->logo; ?>"/><BR />

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer4" value="Enregistrer"/>
<input type="submit" name="Reset4" value="Réinitialisé"/>
</form>

<H1>Logo (pied de page)</H1>
<form name="Form_2" action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoFooter->logo; ?>"/><BR />
<input type="text" name="lien" value="<?php echo $ParamLogoFooter->lien; ?>" placeholder="Lien au clic" onChange="submitForm2()" /><div id="Affichage2"></div><BR /><BR />

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer" value="Enregistrer"/>
<input type="submit" name="Reset" value="Réinitialisé"/>
</form>

<H1>Logo (Navigation)</H1>
<form name="Form_3" action="" method="POST" enctype="multipart/form-data">

<img src="<?php echo $ParamLogoNav->logo; ?>"/><BR />
<input type="text" name="lien" value="<?php echo $ParamLogoNav->lien; ?>" placeholder="Lien au clic" onChange="submitForm3()" /><div id="Affichage3"></div><BR /><BR />

<input type="file" name="photo" placeholder="Votre logo"/><BR /><BR />

<input type="submit" name="Enregistrer3" value="Enregistrer"/>
<input type="submit" name="Reset3" value="Réinitialisé"/>
</form>

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>