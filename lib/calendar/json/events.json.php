<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
header('Content-type: text/json');

$SelectCalendrier=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Calendrier WHERE statue=1");
$SelectCalendrier->execute();
$CountEvent=$SelectCalendrier->rowCount();
$Compteur=1;

echo '[';
while ($Evenement=$SelectCalendrier->fetch(PDO::FETCH_OBJ)) {
    if ($CountEvent==1) {
        echo '	{ "date": "'.$Evenement->created.'000", "type": "Evenement", "title": "'.$Evenement->titre.'", "description": "'.$Evenement->description.'", "url": "'.$Evenement->lien.'" }';
    }
    else {
        if ($CountEvent==$Compteur) {
            echo '	{ "date": "'.$Evenement->created.'000", "type": "Evenement", "title": "'.$Evenement->titre.'", "description": "'.$Evenement->description.'", "url": "'.$Evenement->lien.'" }';
        }
        else {
            echo '	{ "date": "'.$Evenement->created.'000", "type": "Evenement", "title": "'.$Evenement->titre.'", "description": "'.$Evenement->description.'", "url": "'.$Evenement->lien.'" },';
        }
    }
    $Compteur++;
}
echo ']';

?>