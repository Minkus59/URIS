<?php 
$PageActu=$_SERVER['SCRIPT_URL'];
//Recup des articles de la page courante
$RecupArticle=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Article WHERE page=:page AND statue='1' ORDER BY position ASC");
$RecupArticle->bindParam(':page', $PageActu, PDO::PARAM_STR);
$RecupArticle->execute();
$Count=$RecupArticle->rowcount();

//Recup du menu principal actif
$SelectPageActif=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1' AND sous_menu='0' ORDER BY position ASC");
$SelectPageActif->execute();

//Recup du menu footer actif
$SelectPageActifFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1' AND sous_menu='0' ORDER BY position");
$SelectPageActifFooter->execute();
$CountFooter=$SelectPageActifFooter->rowCount();
$Moitier=round(($CountFooter/2)-2);

$SelectPageActifFooter1=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1' AND sous_menu='0' ORDER BY position ASC LIMIT 0, :moitier");
$SelectPageActifFooter1->bindParam(':moitier', $Moitier, PDO::PARAM_INT);
$SelectPageActifFooter1->execute();

$SelectPageActifFooter2=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE statue='1' AND sous_menu='0' ORDER BY position ASC LIMIT :moitier, :CountFooter");
$SelectPageActifFooter2->bindParam(':moitier', $Moitier, PDO::PARAM_INT);
$SelectPageActifFooter2->bindParam(':CountFooter', $CountFooter, PDO::PARAM_INT);
$SelectPageActifFooter2->execute();

//Recup du referencement
$SelectPageSOE=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:page");
$SelectPageSOE->bindParam(':page', $PageActu, PDO::PARAM_STR);
$SelectPageSOE->execute();
$SOEPage=$SelectPageSOE->fetch(PDO::FETCH_OBJ);

//Recup du libelle Accueil
$HOME=$Home."/";
$SelectLibeleAccueil=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:lien");
$SelectLibeleAccueil->bindParam(':lien', $HOME, PDO::PARAM_STR);
$SelectLibeleAccueil->execute();
$PageLibeleAccueil=$SelectLibeleAccueil->fetch(PDO::FETCH_OBJ);

//Recup du libelle Contact
$CONTACT=$Home."/Contact/";
$SelectLibeleContact=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE lien=:lien");
$SelectLibeleContact->bindParam(':lien', $CONTACT, PDO::PARAM_STR);
$SelectLibeleContact->execute();
$PageLibeleContact=$SelectLibeleContact->fetch(PDO::FETCH_OBJ);

//Recup du logo footer
$SelectParamLogoFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='1'");    
$SelectParamLogoFooter->execute(); 
$ParamLogoFooter=$SelectParamLogoFooter->fetch(PDO::FETCH_OBJ);

//Recup du logo principal
$SelectParamLogoHeader=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='2'");    
$SelectParamLogoHeader->execute(); 
$ParamLogoHeader=$SelectParamLogoHeader->fetch(PDO::FETCH_OBJ);

//Recup du logo nav
$SelectParamLogoNav=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='3'");    
$SelectParamLogoNav->execute(); 
$ParamLogoNav=$SelectParamLogoNav->fetch(PDO::FETCH_OBJ);

//Recup du logo region
$SelectParamLogoRegion=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Logo WHERE id='4'");    
$SelectParamLogoRegion->execute(); 
$ParamLogoRegion=$SelectParamLogoRegion->fetch(PDO::FETCH_OBJ);

//Recup du destinataire mailing
$RecupEmailDestinataire=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_mailing_Parametre");
$RecupEmailDestinataire->execute();
$EmailDestinataire=$RecupEmailDestinataire->fetch(PDO::FETCH_OBJ);

//Recup du Information divers
$RecupinfoDivers=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Information");
$RecupinfoDivers->execute();
$InfoDivers=$RecupinfoDivers->fetch(PDO::FETCH_OBJ);
?>