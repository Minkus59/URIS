<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if ($Cnx_Admin!=TRUE) {
  header('location:'.$Home.'/Admin');
}

$Erreur=$_GET['erreur'];
$Valid=$_GET['valid'];
$Groupe=urldecode($_GET['groupe']);
$Selection=$_POST['selection'];
$Compteur=count($Selection);

$SelectGroupe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Groupe WHERE id=:id");
$SelectGroupe->bindParam(':id', $Groupe, PDO::PARAM_STR);
$SelectGroupe->execute();
$GroupeLibele=$SelectGroupe->fetch(PDO::FETCH_OBJ);

if (isset($_POST['Importer'])) {
    $ext = array('.csv', '.CSV');
    $ext_origin=strchr($_FILES['fichier']['name'], '.');
    
        if (!in_array($ext_origin, $ext)) {
           $Erreur="Ce n'est pas un fichier de type .csv<BR />";
        }
        else {
            //Process the CSV file
            $handle = fopen($_FILES['fichier']['tmp_name'], "r");
            $Compteur=0;
            while (($data = fgetcsv($handle, 0, ";")) !== FALSE) {
                if($Compteur!=0) {
                    $Categorie= $data[0];
                    $Alumni= $data[1];
                    $Statut= $data[2];
                    $Nom = $data[3];
                    $Prenom = $data[4];
                    $Email = $data[5];
                    $Liste = $data[6];
    
                    $Select=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
                    $Select->bindParam(':email', $Email, PDO::PARAM_STR);
                    $Select->bindParam(':liste', $Liste, PDO::PARAM_STR);
                    $Select->execute();
                    $Count=$Select->rowCount();
    
                    if($Count==0) {
                        $Ajout=$cnx->prepare("INSERT INTO ".$Prefix."neuro_mailing_Liste_Diffusion (categorie, alumni, statut, nom, prenom, email, liste) VALUES(:categorie, :alumni, :statut, :nom, :prenom, :email, :liste)");
                        $Ajout->bindParam(':categorie', $Categorie, PDO::PARAM_STR);
                        $Ajout->bindParam(':alumni', $Alumni, PDO::PARAM_STR);
                        $Ajout->bindParam(':statut', $Statut, PDO::PARAM_STR);
                        $Ajout->bindParam(':nom', $Nom, PDO::PARAM_STR);
                        $Ajout->bindParam(':prenom', $Prenom, PDO::PARAM_STR);
                        $Ajout->bindParam(':email', $Email, PDO::PARAM_STR);
                        $Ajout->bindParam(':liste', $Liste, PDO::PARAM_STR);
                        $Ajout->execute();
                    }
                }
                $Compteur++;
            }
    
            $Valid="E-mail ajoutée avec succès";
            header('Location:'.$Home.'/Admin/Mailing/ListeDiffusion/?valid='.urlencode($Valid));
        }
    }

//Moteur de recherche
if (isset($_POST['classementNom'])) {
    $_SESSION['classementNom'] = $_POST['classementNom'];
}
else {
    if (isset($_SESSION['classementNom'])) {
        $_SESSION['classementNom']==$_SESSION['classementNom'];
    }
    else {
        $_SESSION['classementNom']='nom';
    }
}

if (isset($_POST['classementSens'])) {
    $_SESSION['classementSens'] = $_POST['classementSens'];
}
else {
    if (isset($_SESSION['classementSens'])) {
        $_SESSION['classementSens']==$_SESSION['classementSens'];
    }
    else {
        $_SESSION['classementSens']='ASC';
    }
}

if (isset($_POST['MoteurRecherche'])) {
    if ($_POST['RechercheDiffusion']=="") {
        $RechercheDiffusion=4;
    }

    if ($_POST['RechercheCategorie']!="") {
        $RechercheCategorie=trim($_POST['RechercheCategorie']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE categorie=:categorie ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute(array(':categorie'=> $RechercheCategorie)); 
    }
    elseif (!empty($_POST['RechercheAlumni'])) {
        $RechercheAlumni=trim($_POST['RechercheAlumni']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE alumni LIKE :alumni ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute(array(':alumni' => "%".$RechercheAlumni."%")); 
    }
    elseif ($_POST['RechercheStatut']!="") {
        $RechercheStatut=trim($_POST['RechercheStatut']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE statut=:statut ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);;
        $SelectListe->execute(array(':statut' =>$RechercheStatut)); 
    }
    elseif (!empty($_POST['RechercheNom'])) {
        $RechercheNom=trim($_POST['RechercheNom']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE nom LIKE :nom ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute(array(':nom' => "%".$RechercheNom."%")); 
    }
    elseif (!empty($_POST['RecherchePrenom'])) {
        $RecherchePrenom=trim($_POST['RecherchePrenom']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE prenom LIKE :prenom ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute(array(':prenom' => "%".$RecherchePrenom."%")); 
    }
    elseif (!empty($_POST['RechercheEmail'])) {
        $RechercheEmail=trim($_POST['RechercheEmail']);
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste WHERE email=:email ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute(array(':email' => $RechercheEmail)); 
    }
    elseif ($_POST['RechercheDiffusion']!="") {
        $RechercheDiffusion=$_POST['RechercheDiffusion'];
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute();
    }
    else {
        $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
        $SelectListe->execute();
    }
}
else {
    $SelectListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste ORDER BY ".$_SESSION['classementNom']." ".$_SESSION['classementSens']);
    $SelectListe->execute();
    $RechercheDiffusion=4;
}

?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/head.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/header.inc.php"); ?>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/menu.inc.php"); ?>

<article>
<?php if (isset($Erreur)) { echo "<font color='#FF0000'>".urldecode($Erreur)."</font><BR />"; }
if (isset($Valid)) { echo "<font color='#009900'>".urldecode($Valid)."</font><BR />"; } ?>

<H2>Ajout un fichier CSV</H2>

<form name="form_ajout" action="" method="POST" enctype="multipart/form-data">
<input type="file" name="fichier" placeholder="Fichier CSV" required/><BR />
(Fichier .CSV comportant les colonnes "categorie", "alumni", "statut", "nom", "prenom", "email" et "liste", la preniere ligne ne sera pas importé, encodage:UTF-8)
<BR />

<input type="submit" name="Importer" value="Importer" class="normal"/>
</form>

<H2>Liste de diffusion <?php echo $GroupeLibele->liste; ?></H2>   

<table class="Admin" width=900>
<tr>
<th>
        Catégorie
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="categorie"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="categorie"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Alumni
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="alumni"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="alumni"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Statut
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="statut"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="statut"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Nom
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="nom"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="nom"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Prénom
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="prenom"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="prenom"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Email
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="email"/>
        <input type="hidden" name="classementSens" value="ASC"/>
        <input type="submit" name="Classement" value="" Class="ASC"/>
        </form>
        <form name="form_classement" action="" method="POST">
        <input type="hidden" name="classementNom" value="email"/>
        <input type="hidden" name="classementSens" value="DESC"/>
        <input type="submit" name="Classement" value="" Class="DESC"/>
        </form>
    </th>
    <th>
        Liste
    </th>
    <th>
        Diffusion
    </th>
    <th>
        Ajouter / Supprimer
    </th>
    <th>
        Action
    </th>
</tr>
<form name="form_recherche" action="" method="POST">
<tr>
    <th>
        <select name="RechercheCategorie" class="mini">
        <option value="">-- --</option>
        <option value="Mi">Mi</option>
        <option value="Pers morale">Pers morale</option>
        <option value="Membre associé">Membre associé</option>
        <option value="Junior">Junior</option>
        <option value="Institution">Institution</option>
        </select>
    </th>
    <th>
        <input type="text" name="RechercheAlumni" class="Moyen"/>
    </th>
    <th>
        <select name="RechercheStatut" class="mini">
        <option value="">-- --</option>
        <option value="Président">Président</option>
        <option value="Trésorier">Trésorier</option>
        <option value="Secrétaire">Secrétaire</option>
        <option value="Webmaster">Webmaster</option>
        </select>
    </th>
    <th>
        <input type="text" name="RechercheNom" class="Moyen"/>
    </th>
    <th>
        <input type="text" name="RecherchePrenom" class="Moyen"/>
    </th>
    <th>
        <input type="text" name="RechercheEmail" class="Moyen"/>
    </th>
        <td></td>
    <td>
        <select name="RechercheDiffusion" class="SelectMini">
        <option value="">-- --</option>
        <option value="0">0</option>
        <option value="1">1</option>
        </select>
    </td>
    <th colspan='2'>
        <input type="submit" name="MoteurRecherche" value="Rechercher" class="normal"/>
    </th>
</tr>
</form>
<form name="liste" action="/Admin/Mailing/ListeDiffusion/liste.php?groupe=<?php echo $Groupe; ?>" method="POST">

<tr>
<td></td><td></td><td></td><td></td><td></td><td></td><td></td>
<td>Tout cocher : </td>
    <td>
        <input type="checkbox" onclick="cocher1()" />
    </td>
    <td colspan="2">
        <input type="submit" name="Modifier" value="" title="Mettre à jour" class="maj"/>
        <input type="submit" name="Ajouter" value="" title="Ajouter la selection" class="ajout"/>
        <input type="submit" name="ExporterListe" value="" title="Exporter la liste" class="exporter"/>
        <input type="submit" name="Supprimer" value="" title="Supprimer la selection" class="suppr"/>
    </td>
</tr>

<?php
while($ListeDiffusion=$SelectListe->fetch(PDO::FETCH_OBJ)) { 
    $SelectDiffusionListe=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Liste_Diffusion WHERE email=:email AND liste=:liste");
    $SelectDiffusionListe->bindParam(':email', $ListeDiffusion->email, PDO::PARAM_STR);
    $SelectDiffusionListe->bindParam(':liste', $GroupeLibele->liste, PDO::PARAM_STR);
    $SelectDiffusionListe->execute();
    $DiffusionListe=$SelectDiffusionListe->fetch(PDO::FETCH_OBJ);
    $CountDiffusionListe=$SelectDiffusionListe->rowCount();
    
    if($RechercheDiffusion==1) {
        if ($DiffusionListe->diffusion==1) {
            ?>
        <tr <?php if ($DiffusionListe->diffusion==1) { echo 'class="vert"'; } elseif ($DiffusionListe->diffusion==2) { echo 'class="gris"'; } else { echo 'class="rouge"'; } ?> >
        <td>
        <?php echo $ListeDiffusion->categorie; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->alumni; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->statut; ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->nom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->prenom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->email); ?>
        </td>
        <td>
            <?php echo $DiffusionListe->liste ?>
        </td>
        <td>
            <?php echo $DiffusionListe->diffusion ?>
        </td>
        <td>
            <input type="checkbox" name="selection[]" value="<?php echo $ListeDiffusion->email; ?>" <?php if ($CountDiffusionListe==1) { echo "checked"; } ?>/>
        </td>
        <td>
            <?php
            if ($DiffusionListe->diffusion==1) { 
                echo '<a title="Désactiver" href="'.$Home.'/Admin/Mailing/ListeDiffusion/desactiver.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/desactiver.png" alt="Désactiver"></a>';
            } 
            else { 
                echo '<a title="Activer" href="'.$Home.'/Admin/Mailing/ListeDiffusion/activer.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/activer.png" alt="Activer"></a>';
            } 
            ?>
        </td>
    </tr>
    <?php
        }
        else { 
        
        }
    }
    elseif($RechercheDiffusion==0) {
        if ($DiffusionListe->diffusion!=1) {
            ?>
        <tr <?php if ($DiffusionListe->diffusion==1) { echo 'class="vert"'; } elseif ($DiffusionListe->diffusion==2) { echo 'class="gris"'; } else { echo 'class="rouge"'; } ?> >
        <td>
        <?php echo $ListeDiffusion->categorie; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->alumni; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->statut; ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->nom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->prenom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->email); ?>
        </td>
        <td>
            <?php echo $DiffusionListe->liste ?>
        </td>
        <td>
            <?php echo $DiffusionListe->diffusion ?>
        </td>
        <td>
            <input type="checkbox" name="selection[]" value="<?php echo $ListeDiffusion->email; ?>" <?php if ($CountDiffusionListe==1) { echo "checked"; } ?>/>
        </td>
        <td>
            <?php
            if ($DiffusionListe->diffusion==1) { 
                echo '<a title="Désactiver" href="'.$Home.'/Admin/Mailing/ListeDiffusion/desactiver.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/desactiver.png" alt="Désactiver"></a>';
            } 
            else { 
                echo '<a title="Activer" href="'.$Home.'/Admin/Mailing/ListeDiffusion/activer.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/activer.png" alt="Activer"></a>';
            } 
            ?>
        </td>
    </tr>
    <?php
        }
        else { 
        
        }
    }
    else { ?>
        <tr <?php if ($DiffusionListe->diffusion==1) { echo 'class="vert"'; } elseif ($DiffusionListe->diffusion==2) { echo 'class="gris"'; } else { echo 'class="rouge"'; } ?> >
        <td>
        <?php echo $ListeDiffusion->categorie; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->alumni; ?>
        </td>
        <td>
            <?php echo $ListeDiffusion->statut; ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->nom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->prenom); ?>
        </td>
        <td>
            <?php echo stripslashes($ListeDiffusion->email); ?>
        </td>
        <td>
            <?php echo $DiffusionListe->liste ?>
        </td>
        <td>
            <?php echo $DiffusionListe->diffusion ?>
        </td>
        <td>
            <input type="checkbox" name="selection[]" value="<?php echo $ListeDiffusion->email; ?>" <?php if ($CountDiffusionListe==1) { echo "checked"; } ?>/>
        </td>
        <td>
            <?php
            if ($DiffusionListe->diffusion==1) { 
                echo '<a title="Désactiver" href="'.$Home.'/Admin/Mailing/ListeDiffusion/desactiver.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/desactiver.png" alt="Désactiver"></a>';
            } 
            else { 
                echo '<a title="Activer" href="'.$Home.'/Admin/Mailing/ListeDiffusion/activer.php?id='.$DiffusionListe->id.'&groupe='.$Groupe.'"><img src="'.$Home.'/Admin/lib/img/activer.png" alt="Activer"></a>';
            } 
            ?>
        </td>
    </tr>
    <?php
    }
}
?>

</form>
</table>
<BR /><BR /><BR />

</article>

<?php require_once($_SERVER['DOCUMENT_ROOT']."/Admin/lib/script/footer.inc.php"); ?>