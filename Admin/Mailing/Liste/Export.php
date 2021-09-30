<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste ORDER BY nom ASC");
$Select->execute();

$Ouverture = fopen("contact.csv", "w+");
fputcsv($Ouverture, array('categorie', 'alumni', 'statut', 'nom', 'prenom', 'email'), ';');

while($AjoutListe=$Select->fetch(PDO::FETCH_OBJ)) {
    fputcsv($Ouverture, array($AjoutListe->categorie, $AjoutListe->alumni, $AjoutListe->statut, $AjoutListe->nom, $AjoutListe->prenom, $AjoutListe->email), ';');
}
fclose($Ouverture);

header("Content-Type: application/force-download");
header('Content-Disposition: attachment; filename="contact.csv"');
header('Content-Length: '.  filesize('contact.csv'));
readfile('contact.csv');
?>