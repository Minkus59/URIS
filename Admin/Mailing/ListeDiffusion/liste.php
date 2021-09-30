<?php
require($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");
require($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");

$Groupe=urldecode($_GET['groupe']);
$Selection=$_POST['selection'];
$Compteur=count($Selection);

$SelectGroupe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Groupe WHERE id=:id");
$SelectGroupe->bindParam(':id', $Groupe, PDO::PARAM_STR);
$SelectGroupe->execute();
$GroupeLibele=$SelectGroupe->fetch(PDO::FETCH_OBJ);

if (isset($_POST['ExporterListe'])) {
    $Ouverture = fopen("diffusion.csv", "w+");
    fputcsv($Ouverture, array('categorie', 'alumni', 'statut', 'nom', 'prenom', 'email', 'liste'), ';');

    $Selection=$_POST['selection'];
    $Compteur=count($Selection);

    for($u=0;$u<$Compteur;$u++) {
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
        $SelectListe->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
        $SelectListe->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
        $SelectListe->execute();
        $AjoutListe=$SelectListe->fetch(PDO::FETCH_OBJ);

        fputcsv($Ouverture, array($AjoutListe->categorie, $AjoutListe->alumni, $AjoutListe->statut, $AjoutListe->nom, $AjoutListe->prenom, $AjoutListe->email, $AjoutListe->liste), ';');
    }

    fclose($Ouverture);

    header("Content-Type: application/force-download");
    header('Content-Disposition: attachment; filename="diffusion.csv"');
    header('Content-Length: '.  filesize('diffusion.csv'));
    readfile('diffusion.csv');
}


if (isset($_POST['Modifier'])) {

    //On supprime toutes les email de la liste pour ajouter la nouvelle selection
    $delete=$cnx->prepare("DELETE FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE liste=:liste AND diffusion!=2");
    $delete->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
    $delete->execute();

    //On ajoute toutes les nouvelles adresses sauf ceux existante non diffuser
    for($u=0;$u<$Compteur;$u++) {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
        $Select->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
        $Select->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
        $Select->execute();
        $Count=$Select->rowCount();

        if($Count==0) {
            $SelectData=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE email=:email");
            $SelectData->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
            $SelectData->execute();
            $Data=$SelectData->fetch(PDO::FETCH_OBJ);

            $Hash = md5(uniqid(rand(), true));
            $Ajout=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Liste_Diffusion (categorie, alumni, statut, nom, prenom, email, liste, hash) VALUES(:categorie, :alumni, :statut, :nom, :prenom, :email, :liste, :hash)");
            $Ajout->bindParam(':categorie', $Data->categorie, PDO::PARAM_STR);
            $Ajout->bindParam(':alumni', $Data->alumni, PDO::PARAM_STR);
            $Ajout->bindParam(':statut', $Data->statut, PDO::PARAM_STR);
            $Ajout->bindParam(':nom', $Data->nom, PDO::PARAM_STR);
            $Ajout->bindParam(':prenom', $Data->prenom, PDO::PARAM_STR);
            $Ajout->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
            $Ajout->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
            $Ajout->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $Ajout->execute();
        }
    }

    $Valid="E-mail ajoutée avec succès";
    header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?groupe='.$Groupe.'&valid='.urlencode($Valid));
}

if (isset($_POST['Ajouter'])) {

    //On ajoute toutes les nouvelles adresses sauf ceux existante non diffuser
    for($u=0;$u<$Compteur;$u++) {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
        $Select->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
        $Select->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
        $Select->execute();
        $Count=$Select->rowCount();

        if($Count==0) {
            $SelectData=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE email=:email");
            $SelectData->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
            $SelectData->execute();
            $Data=$SelectData->fetch(PDO::FETCH_OBJ);

            $Hash = md5(uniqid(rand(), true));
            $Ajout=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Liste_Diffusion (categorie, alumni, statut, nom, prenom, email, liste, hash) VALUES(:categorie, :alumni, :statut, :nom, :prenom, :email, :liste, :hash)");
            $Ajout->bindParam(':categorie', $Data->categorie, PDO::PARAM_STR);
            $Ajout->bindParam(':alumni', $Data->alumni, PDO::PARAM_STR);
            $Ajout->bindParam(':statut', $Data->statut, PDO::PARAM_STR);
            $Ajout->bindParam(':nom', $Data->nom, PDO::PARAM_STR);
            $Ajout->bindParam(':prenom', $Data->prenom, PDO::PARAM_STR);
            $Ajout->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
            $Ajout->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
            $Ajout->bindParam(':hash', $Hash, PDO::PARAM_STR);
            $Ajout->execute();
        }
    }

    $Valid="E-mail ajoutée avec succès";
    header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?groupe='.$Groupe.'&valid='.urlencode($Valid));
}

if (isset($_POST['Supprimer'])) {

    //On ajoute toutes les nouvelles adresses sauf ceux existante non diffuser
    for($u=0;$u<$Compteur;$u++) {
        $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
        $Select->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
        $Select->bindParam(':email', $Selection[$u], PDO::PARAM_STR);
        $Select->execute();
        $Count=$Select->rowCount();
        $Data=$Select->fetch(PDO::FETCH_OBJ);

        if($Count!=0) {
            $delete=$cnx->prepare("DELETE FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE id=:id");
            $delete->bindParam(':id', $Data->id, PDO::PARAM_STR);
            $delete->execute();
        }
    }

    $Valid="E-mail supprimée avec succès";
    header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?groupe='.$Groupe.'&valid='.urlencode($Valid));
}

?>