<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin===false) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];

$chemin = $_FILES['photo']['name'];
$ext = array('.jpeg', '.JPEG', '.jpg', '.JPG', '.png', '.PNG');
$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
$ext2 = array('.png', '.PNG');
$ext_origin = strchr($chemin, '.');
$Id=$_GET['id'];

if (isset($_GET['id'])) { 
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Social WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Carousel=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Ajouter']))&&(in_array($ext_origin, $ext))) {

    // Upload d'image
    $rep = $_SERVER['DOCUMENT_ROOT']."/lib/Social/";
    $fichier = basename($chemin);
    $taille_origin = filesize($_FILES['photo']['tmp_name']);
    $hash = md5(uniqid(rand(), true));
    $Chemin_upload = $Home."/lib/Social/".$hash.$fichier."";
    $TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
    $taille_max = 10000000;
    $Alt=$_POST['alt'];

    if (!file_exists($rep)) {
        mkdir($rep, 0777);
    }

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 880px sur 320px de hauteur";
        ErreurLog($Erreur);
    }

    $Lien=$_POST['lien'];

    if (trim($Lien)=="") {
        $Erreur="Le contenue 'lien' est vide !";
        ErreurLog($Erreur);
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleLargeur_photo = 40;
      $NouvelleHauteur_photo = ( ($TailleImageChoisie[1] * (($NouvelleLargeur_photo)/$TailleImageChoisie[0])) );     


  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

            $InsertPhoto=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Social (logo, lien) VALUES(:logo, :lien");
            $InsertPhoto->bindParam(':logo', $Chemin_upload, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':lien', $Lien, PDO::PARAM_STR);
            $InsertPhoto->execute();

            $Valid="Lien ajouté avec succès !";
            header("location:".$Home."/Admin/Social/Nouveau/?valid=".urlencode($Valid));
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

            $InsertPhoto=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Social (logo, lien) VALUES(:logo, :lien)");
            $InsertPhoto->bindParam(':logo', $Chemin_upload, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':lien', $Lien, PDO::PARAM_STR);
            $InsertPhoto->execute();

            $Valid="Lien ajouté avec succès !";
            header("location:".$Home."/Admin/Social/Nouveau/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    $Lien=$_POST['lien'];

    if (trim($Lien)=="") {
        $Erreur="Le contenue 'lien' est vide !";
        ErreurLog($Erreur);
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Social SET lien=:lien WHERE id=:id");
        $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
        $Insert->BindParam(":lien", $Lien, PDO::PARAM_STR); 
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
            ErreurLog($Erreur);
        }
        else  {     
            $Valid="Lien modifier avec succès";
            header('location:'.$Home.'/Admin/Social/Nouveau/?id='.$Id.'&valid='.urlencode($Valid));
        }
    }
} 
    
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article class="REEL">
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR />"; }   ?>

<?php if (isset($_GET['id'])) { ?>
      <H1>Modifier le lien</H1> <?php
} else { ?>
  <H1>Ajouter une photo</H1><?php
} ?>

<form name="form_photo" action="" method="POST" enctype="multipart/form-data">

<?php if (!isset($_GET['id'])) { ?>
<input type="file"  placeholder="Photo" name="photo"/><BR />
(Taille conseillé : largeur 40px sur 40px de hauteur)<BR /><BR />
<?php } ?>

<input type="text" name="lien" placeholder="Lien" value="<?php if (isset($_GET['id'])) { echo $Carousel->lien; } ?>"><BR /><BR />

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/> <?php } else { ?><input type="submit" name="Ajouter" value="Ajouter"/><?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>