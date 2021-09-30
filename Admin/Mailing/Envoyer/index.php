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

$RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Parametre");
$RecupParam->execute();
$ParamEmail=$RecupParam->fetch(PDO::FETCH_OBJ);  

if (isset($_POST['type'])) {
    $_SESSION['type']=$_POST['type'];
}

if (isset($_SESSION['type'])) {
    $RecupParam=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Predefini WHERE id=:id");
    $RecupParam->BindParam(":id", $_SESSION['type'], PDO::PARAM_STR);
    $RecupParam->execute();
    $Param=$RecupParam->fetch(PDO::FETCH_OBJ); 
}

if (isset($_POST['signature'])) {
    $_SESSION['signature']=$_POST['signature'];
}

if (isset($_SESSION['signature'])) {
    $RecupSignature=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Signature WHERE id=:id");
    $RecupSignature->BindParam(":id", $_SESSION['signature'], PDO::PARAM_STR);
    $RecupSignature->execute();
    $ParamSignature=$RecupSignature->fetch(PDO::FETCH_OBJ); 
}

if (isset($_POST['destinataire'])) {
    $_SESSION['destinataire']=$_POST['destinataire'];
}

if (isset($_POST['Nb-pj'])) {
    $_SESSION['Nb-pj']=$_POST['Nb-pj'];
}

if (isset($_POST['retour'])) {
    $_SESSION['retour']=$_POST['retour'];
}

if (!isset($_SESSION['retour'])) {
    $_SESSION['retour']=$ParamEmail->email;
}

if (isset($_POST['objet'])) {
    $_SESSION['objet']=$_POST['objet'];
}

if (isset($_POST['groupe'])) {
    $_SESSION['groupe']=$_POST['groupe'];
}

if (isset($_POST['message'])) {
    $_SESSION['message']=$_POST['message'];
}

echo $Retour;

if ((isset($_POST['Envoyer']))&&($_POST['Envoyer']=="Envoyer")) { 
    $_SESSION['message']=$_POST['message'];

    if ((isset($_SESSION['objet']))&&(!empty($_SESSION['objet']))) {      
        if ((isset($_SESSION['message']))&&(!empty($_SESSION['message']))) {               
            if ((isset($_SESSION['Nb-pj']))&&($_SESSION['Nb-pj']!=0)) {
                for ($n=0;$n<=$_SESSION['Nb-pj'];$n++) {

                    if ((isset($_FILES['fichier'.$n]['name']))&&(!empty($_FILES['fichier'.$n]['name']))) {
                    
                        $Fichier[$n]=$_FILES['fichier'.$n]['name'];
                        $FichierTmp[$n]=$_FILES['fichier'.$n]['tmp_name'];
                        $NomFichier[$n]=basename($Fichier[$n]);
                        $Taille[$n]=filesize($FichierTmp[$n]);
                        $ExtOrigin[$n]=strchr($Fichier[$n], '.');
                        $TailleMax="20000000";
                        
                        $Code[$n]=md5(uniqid(rand(), true));
                        $Hash[$n]=substr($Code[$n], 0, 8);

                        $RepInt=$_SERVER['DOCUMENT_ROOT']."/lib/Mail/Document/";
                        $RepExt=$Home."/lib/Mail/Document/";
                        
                        //upload fichier
                        
                        $Upload[$n]= move_uploaded_file($FichierTmp[$n], $RepInt.$Hash[$n].$ExtOrigin[$n]);

                        if ($Upload[$n]==FALSE) {
                            $Erreur="Erreur de téléchargement du fichier, veuillez réassayer ultérueurement";
                        }
                        else {
                            $CheminFichier[$n] = $RepInt.$Hash[$n].$ExtOrigin[$n];
                            // Pièce jointe
                            $content[$n] = file_get_contents($CheminFichier[$n]);
                            $content[$n] = chunk_split(base64_encode($content[$n]));
                        }
                    }
                }
            }

            if ($_SESSION['groupe']=="Destinataire") {
                if ((isset($_SESSION['destinataire']))&&(!empty($_SESSION['destinataire']))) {
                    $Destinataire2=$_SESSION['destinataire']=$_SESSION['destinataire'];

                    if (!preg_match("#^[a-z0-9._-]+@(dbmail|hotmail|live|msn|outlook).[a-z]{2,4}$#", $Destinataire2)) {
                        $passage_ligne = "\r\n";
                    }
                    else {
                        $passage_ligne = "\n";
                    }

                    $Retour=$_SESSION['retour'];
                    $boundary = md5(uniqid(mt_rand()));

                    $Entete = "From: \"$Societe\" <$Retour>".$passage_ligne;
                    $Entete.= "Reply-to: \"$Societe\" <$Retour>".$passage_ligne;
                    $Entete.= "MIME-Version: 1.0".$passage_ligne;
                    $Entete.= "Content-Type: multipart/mixed; boundary=".$boundary." ".$passage_ligne;
                    
                    $message="--".$boundary.$passage_ligne;
                    $message.="Content-Type: text/html; charset=utf-8".$passage_ligne; 
                    $message.="Content-Transfer-Encoding: 8bit".$passage_ligne;

                    $message.="<html><head>
                                <title>".$_SESSION['objet']."</title>
                                </head>
                                <body>
                                ".$_SESSION['message']."
                                </body>
                                </html>".$passage_ligne;
                                
                    if ((isset($_SESSION['Nb-pj']))&&($_SESSION['Nb-pj']!=0)) {
                        for ($n=0;$n<=$_SESSION['Nb-pj'];$n++) {

                            if ((isset($_FILES['fichier'.$n]['name']))&&(!empty($_FILES['fichier'.$n]['name']))) {
                                $message.="--".$boundary.$passage_ligne; 

                                if ($Upload[$n]==TRUE) {
                                    if (in_array($ExtOrigin[$n], array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                        $message.= "Content-Type: image/jpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".png", ".PNG"))) {                  
                                        $message.= "Content-Type: image/png;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".gif", ".GIF"))) {                  
                                        $message.= "Content-Type: image/jpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".doc", ".DOC"))) {                  
                                        $message.= "Content-Type: application/msword;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".pdf", ".PDF"))) {                  
                                        $message.= "Content-Type: application/pdf;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }  
                                    if (in_array($ExtOrigin[$n], array(".rtf", ".RFT"))) {                  
                                        $message.= "Content-Type: application/rtf;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".xls", ".XLS"))) {                  
                                        $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".ppt", ".PPT"))) {                  
                                        $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }      
                                    if (in_array($ExtOrigin[$n], array(".zip", ".ZIP"))) {                  
                                        $message.= "Content-Type: application/zip;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    } 
                                    if (in_array($ExtOrigin[$n], array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                        $message.= "Content-Type: image/tiff;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".avi", ".AVI"))) {                  
                                        $message.= "Content-Type: video/msvideo;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                        $message.= "Content-Type: video/quicktime;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                        $message.= "Content-Type: video/mpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    
                                    $message.= "Content-Transfer-Encoding: base64".$passage_ligne;
                                    $message.= "Content-Disposition:attachment;filename=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    
                                    $message.=$passage_ligne."$content[$n]".$passage_ligne;      
                                    
                                    $message.=$passage_ligne."--".$boundary.$passage_ligne;                   
                                }
                            }
                        }
                    }

                    if (mail($Destinataire2, $_SESSION['objet'], $message, $Entete)===FALSE) {
                        $Erreur = "L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement ! Email = ".$Destinataire2;
                        ErreurLogMail($Erreur);
                    }
                    else {     
                        //Ajout au Log
                        $Log = "L'e-mail a été envoyé à : ".$Desti->email;
                        LogMail($Log);

                        //Ajout historique
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Historique (destinataire, objet, message, retour, type, created) VALUES(:destinataire, :objet, :message, :retour, :type, :created)");
                        $Insert->BindParam(":destinataire", $Destinataire2, PDO::PARAM_STR);
                        $Insert->BindParam(":objet", $_SESSION['objet'], PDO::PARAM_STR);
                        $Insert->BindParam(":message", $_SESSION['message'], PDO::PARAM_STR);
                        $Insert->BindParam(":retour", $_SESSION['retour'], PDO::PARAM_STR);
                        $Insert->BindParam(":type", $_SESSION['type'], PDO::PARAM_STR);
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->execute();

                        unset($_SESSION['destinataire']);
                        unset($_SESSION['type']);
                        unset($_SESSION['signature']);
                        unset($_SESSION['groupe']);
                        unset($_SESSION['objet']);
                        unset($_SESSION['message']);
                        unset($_SESSION['retour']);
                        unset($_SESSION['Nb-pj']);

                        $Valid="Votre message a bien été envoyé !";
                        header("location:".$Home."/Admin/Mailing/Envoyer/?valid=".urlencode($Valid));
                    }
                }
            }
            else {
                $SelectDesti=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE liste=:liste AND diffusion=1");
                $SelectDesti->bindParam(':liste', $_SESSION['groupe'], PDO::PARAM_STR);
                $SelectDesti->execute();
                $Retour=$_SESSION['retour'];
                $boundary = md5(uniqid(mt_rand()));

                while($Desti=$SelectDesti->fetch(PDO::FETCH_OBJ)) {

                    if (!preg_match("#^[a-z0-9._-]+@(dbmail|hotmail|live|msn|outlook).[a-z]{2,4}$#", $Desti->email)) {
                        $passage_ligne = "\r\n";
                    }
                    else {
                        $passage_ligne = "\n";
                    }

                    $Entete = "From: \"$Societe\" <$Retour>".$passage_ligne;
                    $Entete.= "Reply-to: \"$Societe\" <$Retour>".$passage_ligne;
                    $Entete.= "MIME-Version: 1.0".$passage_ligne;
                    $Entete.= "Content-Type: multipart/mixed; boundary=".$boundary." ".$passage_ligne;
                    
                    $message="--".$boundary.$passage_ligne;
                    $message.="Content-Type: text/html; charset=utf-8".$passage_ligne; 
                    $message.="Content-Transfer-Encoding: 8bit".$passage_ligne;

                    $message.="<html><head>
                    <title>".$_SESSION['objet']."</title>
                    </head>
                    <body>
                    ".$_SESSION['message']."
                    </body>
                    </html>".$passage_ligne;

                    if ((isset($_SESSION['Nb-pj']))&&($_SESSION['Nb-pj']!=0)) {
                        for ($n=0;$n<=$_SESSION['Nb-pj'];$n++) {

                            if ((isset($_FILES['fichier'.$n]['name']))&&(!empty($_FILES['fichier'.$n]['name']))) {
                                $message.="--".$boundary.$passage_ligne; 

                                if ($Upload[$n]==TRUE) {
                                    if (in_array($ExtOrigin[$n], array(".jpg", ".jpeg", ".jpe", ".JPG", ".JPEG", ".JPE"))) {                  
                                        $message.= "Content-Type: image/jpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".png", ".PNG"))) {                  
                                        $message.= "Content-Type: image/png;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".gif", ".GIF"))) {                  
                                        $message.= "Content-Type: image/jpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".doc", ".DOC"))) {                  
                                        $message.= "Content-Type: application/msword;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".pdf", ".PDF"))) {                  
                                        $message.= "Content-Type: application/pdf;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }  
                                    if (in_array($ExtOrigin[$n], array(".rtf", ".RFT"))) {                  
                                        $message.= "Content-Type: application/rtf;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".xls", ".XLS"))) {                  
                                        $message.= "Content-Type: application/vnd.ms-excel;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".ppt", ".PPT"))) {                  
                                        $message.= "Content-Type: application/vnd.ms-powerpoint;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }      
                                    if (in_array($ExtOrigin[$n], array(".zip", ".ZIP"))) {                  
                                        $message.= "Content-Type: application/zip;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    } 
                                    if (in_array($ExtOrigin[$n], array(".tif", ".tiff", "TIF", "TIFF"))) {                  
                                        $message.= "Content-Type: image/tiff;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".avi", ".AVI"))) {                  
                                        $message.= "Content-Type: video/msvideo;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".mov", ".qt", ".MOV", ".QT"))) {                  
                                        $message.= "Content-Type: video/quicktime;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    if (in_array($ExtOrigin[$n], array(".mpeg", ".mpg", ".mpe", ".MPEG", ".MPG", ".MPE"))) {                  
                                        $message.= "Content-Type: video/mpeg;name=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    }
                                    
                                    $message.= "Content-Transfer-Encoding: base64".$passage_ligne;
                                    $message.= "Content-Disposition:attachment;filename=\"$Hash[$n]$ExtOrigin[$n]\"".$passage_ligne;
                                    
                                    $message.=$passage_ligne."$content[$n]".$passage_ligne;      
                                    
                                    $message.=$passage_ligne."--".$boundary.$passage_ligne;                     
                                }
                            }
                        }
                    }
                    $Erreur.= $Entete."</BR></BR>";
                    $Erreur.= $message."</BR></BR>";
                    if (mail($Desti->email, $_SESSION['objet'], $message, $Entete)===FALSE) {
                        $Erreur = "L'e-mail n'a pu être envoyé, vérifiez que vous l'avez entré correctement ! Email = ".$Desti->email;
                        ErreurLogMail($Erreur);
                        header("location:".$Home."/Admin/Mailing/Envoyer/?erreur=".urlencode($Erreur));
                    }
                    else {
                        //Ajout au Log
                        $Log = "L'e-mail a été envoyé à : ".$Desti->email;
                        LogMail($Log);

                        //Ajout historique
                        $Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Historique (destinataire, objet, message, retour, type, created) VALUES(:destinataire, :objet, :message, :retour, :type, :created)");
                        $Insert->BindParam(":destinataire", $Desti->email, PDO::PARAM_STR);
                        $Insert->BindParam(":objet", $_SESSION['objet'], PDO::PARAM_STR);
                        $Insert->BindParam(":message", $_SESSION['message'], PDO::PARAM_STR);
                        $Insert->BindParam(":retour", $_SESSION['retour'], PDO::PARAM_STR);
                        $Insert->BindParam(":type", $_SESSION['type'], PDO::PARAM_STR);
                        $Insert->BindParam(":created", $Now, PDO::PARAM_STR);
                        $Insert->execute();
                    }
                }
                unset($_SESSION['destinataire']);
                unset($_SESSION['type']);
                unset($_SESSION['signature']);
                unset($_SESSION['groupe']);
                unset($_SESSION['objet']);
                unset($_SESSION['message']);
                unset($_SESSION['retour']);
                unset($_SESSION['Nb-pj']);

                $Valid="Votre message a bien été envoyé !";
                header("location:".$Home."/Admin/Mailing/Envoyer/?valid=".urlencode($Valid));
            }
  
        }
        else {
            $Erreur="Veuillez entrer un message !";
        }
    }
    else {
        $Erreur="Veuillez entrer un objet de message !";
    }
}
    
?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<p><font color='#FF0000'>".urldecode($Erreur)."</font><BR /><BR />"; }
if (isset($Valid)) { echo "<p><font color='#009900'>".urldecode($Valid)."</font><BR /><BR />"; }   ?>

<div id="Form_Middle3">
<H1>Envoyer un e-mail</H1>

<form name="form" action="" method="POST" >
<select name="type" id="type" onChange="submit()">
<option value="NULL">-- Modèle --</option>
<?php 
$mailing=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Predefini");    
$mailing->execute(); 
while($Model=$mailing->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $Model->id; ?>" <?php if ($Model->id==$_SESSION['type']) { echo "selected"; } ?> ><?php echo $Model->libele; ?></option>
<?php } ?>
</select><BR />
</form>

<form name="form" action="" method="POST" >
<select name="signature" id="signature" onChange="submit()">
<option value="NULL">-- Signature --</option>
<?php 
$SignatureListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Signature");    
$SignatureListe->execute(); 
while($ListeSignature=$SignatureListe->fetch(PDO::FETCH_OBJ)) { ?>
    <option value="<?php echo $ListeSignature->id; ?>" <?php if ($ListeSignature->id==$_SESSION['signature']) { echo "selected"; } ?> ><?php echo $ListeSignature->libelle; ?></option>
<?php } ?>
</select><BR />
</form>

<form name="form" action="" method="POST" >
<select name="groupe" id="groupe" onChange="submit()">
<option value="NULL">-- Liste de diffusion --</option>
<option value="Destinataire" <?php if ($_SESSION['groupe']=="Destinataire") { echo "selected"; } ?> >Destinataire</option>
    <?php 
    $GroupeListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Groupe");    
    $GroupeListe->execute(); 
    while($Groupe=$GroupeListe->fetch(PDO::FETCH_OBJ)) { ?>
        <option value="<?php echo $Groupe->liste; ?>" <?php if ($Groupe->liste==$_SESSION['groupe']) { echo "selected"; } ?> ><?php echo $Groupe->liste; ?></option>
    <?php } ?>
</select><BR /><BR />
</form>

<form name="form_destinataire" action="" method="POST" enctype="multipart/form-data">
<?php 
if ($_SESSION['groupe']=="Destinataire") { ?>
    <input type="text" placeholder="Destinataire :" name="destinataire" require="required" value="<?php if (isset($_SESSION['destinataire'])) { echo $_SESSION['destinataire']; } else { echo $Destinataire2; } ?>" onChange="submit()"/><BR />
<?php }
?>
</form>
<form name="form_Objet" action="" method="POST" enctype="multipart/form-data">
<input type="text" placeholder="Objet :" name="objet" require="required" value="<?php echo $_SESSION['objet']; ?>" onChange="submit()"/><BR />
</form>
<form name="form_retour" action="" method="POST" enctype="multipart/form-data">
<input type="text" placeholder="Adresse de retour" name="retour" value="<?php if (isset($_SESSION['retour'])) { echo $_SESSION['retour']; } else { echo $ParamEmail->email; } ?>" require="required" onChange="submit()"/><BR /><BR />
</form>


<form name="form_Nb-pj" action="" method="POST" >
<select name="Nb-pj" id="Nb-pj" onChange="submit()">
<option value="NULL">pièce jointe</option>
<?php for ($n=0;$n<=5;$n++) { ?>
<option value='<?php echo $n; ?>' <?php if ($_SESSION['Nb-pj']==$n) { echo 'selected'; } ?>><?php echo $n; ?></option>
<?php } ?>
</select><BR />
</form>

<form name="form_pj" action="" method="POST" enctype="multipart/form-data">
<?php if ((isset($_SESSION['Nb-pj']))&&($_SESSION['Nb-pj']!=0)) { ?>
    <?php for ($p=1;$p<=$_SESSION['Nb-pj'];$p++) { 
    echo '<input type="file"  placeholder="pièce jointe '.$p.'" name="fichier'.$p.'"/><BR />';
    } 
} ?><BR />

<textarea id="message" name="message" placeholder="Message*" require="required">
<?php 
if (isset($_SESSION['message'])) {
    echo $_SESSION['message'];
} 
else { 
    echo $Param->mailing."
    <BR /><BR />"
    .$ParamSignature->signature; 
} ?>
</textarea><BR />

<input type="submit" name="Envoyer" value="Envoyer" class="normal"/>
</form>
</div>
<BR /><BR /><BR />
</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>