<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste ORDER BY nom ASC");
$Select->execute();

if (isset($_POST['Supprimer'])) {
    $Selection=$_POST['selection'];
    $Compteur=count($Selection);

    for($u=0;$u<$Compteur;$u++) {
        $delete=$cnx->prepare("DELETE FROM ".$Prefix."neuro_mailing_Liste WHERE id=:id");
        $delete->bindParam(':id', $Selection[$u], PDO::PARAM_STR);
        $delete->execute();
    }

    $Valid="E-mail supprimer avec succès";
    header('Location:'.$Home.'/Admin/Mailing/Liste/?valid='.urlencode($Valid));
}

if (isset($_POST['ExporterListe'])) {
    $Ouverture = fopen("contact.csv", "w+");
    fputcsv($Ouverture, array('categorie', 'alumni', 'statut', 'nom', 'prenom', 'email'), ';');

    $Selection=$_POST['selection'];
    $Compteur=count($Selection);

    for($u=0;$u<$Compteur;$u++) {
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE id=:id");
        $SelectListe->bindParam(':id', $Selection[$u], PDO::PARAM_STR);
        $SelectListe->execute();
        $AjoutListe=$SelectListe->fetch(PDO::FETCH_OBJ);

        fputcsv($Ouverture, array($AjoutListe->categorie, $AjoutListe->alumni, $AjoutListe->statut, $AjoutListe->nom, $AjoutListe->prenom, $AjoutListe->email), ';');
    }

    fclose($Ouverture);

    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename="contact.csv"');
    header('Content-Length: '.  filesize('contact.csv'));
    readfile('contact.csv');
}
?>