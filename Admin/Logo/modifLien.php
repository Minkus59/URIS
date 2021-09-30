<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php"); 
header("Access-Control-Allow-Origin: ".$Ajax);

$Id=$_GET['id'];
$Lien=$_POST['lien'];

if (empty($Lien)) {
    echo "<font color='#FF0000'>Un lien doit être saisie !</font>";
}
elseif (strlen($Lien)<=2) {
    echo "<font color='#FF0000'>Le lien doit comporter au moins 2 lettres !</font>";
}
else {
    $Insert=$cnx->prepare("UPDATE ".$Prefix."neuro_Logo SET lien=:lien WHERE id=:id");
    $Insert->bindParam(':lien', $Lien, PDO::PARAM_STR);
    $Insert->bindParam(':id', $Id, PDO::PARAM_STR);
    $Insert->execute();

    echo "<img src='".$Home."/Admin/lib/img/valid.png'/>";
}
?>