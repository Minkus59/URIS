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
    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel WHERE id=:id");
    $Select->BindParam(":id", $Id, PDO::PARAM_STR);
    $Select->execute();
    $Carousel=$Select->fetch(PDO::FETCH_OBJ);
}

if ((isset($_POST['Ajouter']))&&(in_array($ext_origin, $ext))) {

    // Upload d'image
    $rep = $_SERVER['DOCUMENT_ROOT']."/lib/carousel/";
    $fichier = basename($chemin);
    $taille_origin = filesize($_FILES['photo']['tmp_name']);
    $hash = md5(uniqid(rand(), true));
    $Chemin_upload = $Home."/lib/carousel/".$hash.$fichier."";
    $TailleImageChoisie = @getimagesize($_FILES['photo']['tmp_name']);
    $taille_max = 10000000;
    $Alt=$_POST['alt'];

    //verifier si 1er article sinon position +1
    $Verif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel");
    $Verif->execute();
    $NbPhoto=$Verif->rowCount();
    $Position=$NbPhoto+1;

    if (!file_exists($rep)) {
        mkdir($rep, 0777);
    }

    if($taille_origin>$taille_max){
        $Erreur = "fichier trop volumineux, il ne doit dépasser les 10Mo taille conseillé : largeur 880px sur 320px de hauteur";
    }
    if (!isset($Erreur)){       
      //si largeur + grande

      $NouvelleLargeur_photo = 900;
      $NouvelleHauteur_photo = ( ($TailleImageChoisie[1] * (($NouvelleLargeur_photo)/$TailleImageChoisie[0])) );     


  if (in_array($ext_origin, $ext1)) {
 
        $ImageChoisie_photo = imagecreatefromjpeg($_FILES['photo']['tmp_name']);
        $NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
        imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImageChoisie[0],$TailleImageChoisie[1]);

        if (imagejpeg($NouvelleImage_photo , $rep.$hash.$fichier, 100)){

            $InsertPhoto=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Carousel (position, lien, alt) VALUES(:position, :lien, :alt)");
            $InsertPhoto->bindParam(':position', $Position, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':lien', $Chemin_upload, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':alt', $Alt, PDO::PARAM_STR);
            $InsertPhoto->execute();

            $Valid="Photo ajouté avec succès !";
            header("location:".$Home."/Admin/Carousel/Nouveau/?valid=".urlencode($Valid));
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

            $InsertPhoto=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Carousel (position, lien, alt) VALUES(:position, :lien, :alt)");
            $InsertPhoto->bindParam(':position', $Position, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':lien', $Chemin_upload, PDO::PARAM_STR);
            $InsertPhoto->bindParam(':alt', $Alt, PDO::PARAM_STR);
            $InsertPhoto->execute();

            $Valid="Photo ajouté avec succès !";
            header("location:".$Home."/Admin/Carousel/Nouveau/?valid=".urlencode($Valid));
        }
    else { $Erreur="Erreur !"; ErreurLog($Erreur); }    
    }
  }
}

if ((isset($_POST['Modifier']))&&(isset($_GET['id']))) {
    $Position=$_POST['position'];
    $Alt=$_POST['alt'];

    if (trim($Position)=="") {
        $Erreur="Le contenue 'position' est vide !";
        ErreurLog($Erreur);
    }
    else {
        $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Carousel SET position=:position, alt=:alt WHERE id=:id");
        $Insert->BindParam(":id", $Id, PDO::PARAM_STR);
        $Insert->BindParam(":position", $Position, PDO::PARAM_STR);   
        $Insert->bindParam(':alt', $Alt, PDO::PARAM_STR);   
        $Insert->execute();

        if (!$Insert) {
            $Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
            ErreurLog($Erreur);
        }
        else  {     
            $Valid="Position modifier avec succès";
            header('location:'.$Home.'/Admin/Carousel/Nouveau/?id='.$Id.'&valid='.urlencode($Valid));
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
      <H1>Modifier la position</H1> <?php
} else { ?>
  <H1>Ajouter une photo</H1><?php
} ?>

<form name="form_photo" action="" method="POST" enctype="multipart/form-data">

<?php if (isset($_GET['id'])) { ?>
<input type="text" name="position" placeholder="Position*" require="required" value="<?php if (isset($_GET['id'])) { echo $Carousel->position; } ?>"><BR />
<?php } else { ?>

<input type="file"  placeholder="Photo" name="photo"/><BR />
(Taille conseillé : largeur 900px sur 380px de hauteur)<BR /><BR />
<?php } ?>

<input type="text" name="alt" placeholder="Description" value="<?php if (isset($_GET['id'])) { echo $Carousel->alt; } ?>"><BR /><BR />

<?php if (isset($_GET['id'])) { ?><input type="submit" name="Modifier" value="Modifier"/> <?php } else { ?><input type="submit" name="Ajouter" value="Ajouter"/><?php } ?>
</form>
<p><font color='#FF0000'>*</font> Champ de saisie requis

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>