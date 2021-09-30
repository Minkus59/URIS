<?php
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/fonction_perso.inc.php");  
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/redirect.inc.php");
require_once($_SERVER['DOCUMENT_ROOT']."/lib/script/requete.inc.php");

if(isset($_FILES["document"])) {
	$ret = array();
	$custom_error= array();

	$ext1 = array('.jpeg', '.JPEG', '.jpg', '.JPG');
	$ext2 = array('.png', '.PNG');
	$ext3 = array('.pdf', '.PDF');
	$ext4 = array('.avi', '.AVI', '.mov', '.MOV', '.mp4', '.mp4', '.mpg', '.MPG', '.mpa', '.MPA', '.mp2', '.MP2', '.m2p', '.M2P', '.wma', '.WMA', '.asf', '.ASF');
	$ext5 = array('.pps', '.PPS', '.ppsx', '.PPSX');

	$chemin=$_FILES['document']['name'];
	$fichier=basename($chemin);
	$taille_origin=filesize($_FILES['document']['tmp_name']);
	$ext_origin=strchr($chemin, '.');
	$libele=$_POST['libele'];
	
	if (in_array($ext_origin, $ext2)) {
		$Code=md5(uniqid(rand(), true));
		$Hash=substr($Code, 0, 8);
		$TailleImage=@getimagesize($_FILES['document']['tmp_name']);
		$taille_max="20000000";
		$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Photo/";
		$repExt=$Home."/lib/Photo/";
		$Type="Image";
		$Lien=$repExt.$Hash.$ext_origin;
		
		if (!file_exists($repInt)) {
			mkdir($repInt, 0777);
		}

		if($taille_origin>$taille_max) {
			$Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
			ErreurLog($Erreur);
		}

		if (!isset($Erreur)) {
		
			if ($TailleImage[0]>880) {
			  $NouvelleLargeur_photo = 880;                
			} 
			if (($TailleImage[0]>480)&&($TailleImage[0]<880)) {
			  $NouvelleLargeur_photo = 460;               
			} 
			if ($TailleImage[0]<480) {
			  $NouvelleLargeur_photo = $TailleImage[0];                
			}                
										  
			$NouvelleHauteur_photo = ( ($TailleImage[1] * (($NouvelleLargeur_photo)/$TailleImage[0])) );      
			$ImageChoisie_photo = imagecreatefrompng($_FILES['document']['tmp_name']);
			$NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
			imagealphablending($NouvelleImage_photo, false);
			imagesavealpha($NouvelleImage_photo, true);
			imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImage[0],$TailleImage[1]);

			$UpMaqimg=imagepng($NouvelleImage_photo, $repInt.$Hash.$ext_origin, 6);

			if ($UpMaqimg==false) {
				$Erreur="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				ErreurLog($Erreur);
			}
			else {
				$Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Document (libele, lien, type) VALUES(:libele, :lien, :type)");
				$Insert->BindParam(":libele", $libele, PDO::PARAM_STR);
				$Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
				$Insert->BindParam(":type", $Type, PDO::PARAM_STR);
				$Insert->execute();

				if (!$Insert) {
					$custom_error['jquery-upload-file-error']="Erreur serveur, veuillez réessayer ultèrieurement !";
					echo json_encode($custom_error);
					die();
				}
				else  {     
					$ret[]= $chemin;
					echo json_encode($ret);
				}
			}
		} 
	}

	if (in_array($ext_origin, $ext1)) {
		$Code=md5(uniqid(rand(), true));
		$Hash=substr($Code, 0, 8);
		$TailleImage=@getimagesize($_FILES['document']['tmp_name']);
		$taille_max="20000000";
		$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Photo/";
		$repExt=$Home."/lib/Photo/";
		$Type="Image";
		$Lien=$repExt.$Hash.$ext_origin;

		if (!file_exists($repInt)) {
			mkdir($repInt, 0777);
		}

		if($taille_origin>$taille_max) {
			$custom_error['jquery-upload-file-error']="Fichier trop volumineux, il ne doit dépassé les 20Mo";
			echo json_encode($custom_error);
			die();
		}

		if (!isset($Erreur)) {
		
			if ($TailleImage[0]>880) {
			  $NouvelleLargeur_photo = 880;                
			} 
			if (($TailleImage[0]>480)&&($TailleImage[0]<880)) {
			  $NouvelleLargeur_photo = 460;               
			} 
			if ($TailleImage[0]<480) {
			  $NouvelleLargeur_photo = $TailleImage[0];                
			}
			
			$NouvelleHauteur_photo = ( ($TailleImage[1] * (($NouvelleLargeur_photo)/$TailleImage[0])) );
			$ImageChoisie_photo = imagecreatefromjpeg($_FILES['document']['tmp_name']);
			$NouvelleImage_photo = imagecreatetruecolor($NouvelleLargeur_photo , $NouvelleHauteur_photo);
			imagecopyresampled($NouvelleImage_photo , $ImageChoisie_photo, 0, 0, 0, 0, $NouvelleLargeur_photo, $NouvelleHauteur_photo, $TailleImage[0],$TailleImage[1]);

			$UpMaqimg=imagejpeg($NouvelleImage_photo, $repInt.$Hash.$ext_origin, 75);

			if ($UpMaqimg==false) {
				$custom_error['jquery-upload-file-error']="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				echo json_encode($custom_error);
				die();
			}
			else {
				$Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Document (libele, lien, type) VALUES(:libele, :lien, :type)");
				$Insert->BindParam(":libele", $libele, PDO::PARAM_STR);
				$Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
				$Insert->BindParam(":type", $Type, PDO::PARAM_STR);
				$Insert->execute();

				if (!$Insert) {
					$custom_error['jquery-upload-file-error']="Erreur serveur, veuillez réessayer ultèrieurement !";
					echo json_encode($custom_error);
					die();
				}
				else  {     
					$ret[]= $chemin;
					echo json_encode($ret);
				}
			}
		}
	}

	if (in_array($ext_origin, $ext3)) {
		$Code=md5(uniqid(rand(), true));
		$Hash=substr($Code, 0, 8);
		$taille_max="20000000";
		$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Document/";
		$repExt=$Home."/lib/Document/";
		$Type="PDF";
		$Lien=$repExt.$Hash.$ext_origin;

		if($taille_origin>$taille_max) {
			$Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
			ErreurLog($Erreur);
		}

		if (!isset($Erreur)) {
			if (move_uploaded_file($_FILES['document']['tmp_name'], $repInt.$Hash.$ext_origin)==false) {				
				$custom_error['jquery-upload-file-error']="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				echo json_encode($custom_error);
				die();
			}
			else {
				$Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Document (libele, lien, type) VALUES(:libele, :lien, :type)");
				$Insert->BindParam(":libele", $libele, PDO::PARAM_STR);
				$Insert->BindParam(":type", $Type, PDO::PARAM_STR);
				$Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
				$Insert->execute();

				if (!$Insert) {
					$custom_error['jquery-upload-file-error']="Erreur serveur, veuillez réessayer ultèrieurement !";
					echo json_encode($custom_error);
					die();
				}
				else  {     
					$ret[]= $chemin;
					echo json_encode($ret);
				}
			}
		}
	}

	if (in_array($ext_origin, $ext4)) {
		$Code=md5(uniqid(rand(), true));
		$Hash=substr($Code, 0, 8);
		$taille_max="500000000";
		$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/Video/";
		$repExt=$Home."/lib/Video/";
		$Type="Video";
		$Lien=$repExt.$Hash.$ext_origin;

		if (!file_exists($repInt)) {
			mkdir($repInt, 0777);
		}

		if($taille_origin>$taille_max) {
			//$Erreur = "fichier trop volumineux, il ne doit dépassé les 20Mo";
			$custom_error['jquery-upload-file-error']="Fichier trop volumineux, il ne doit dépassé les 20Mo";
			echo json_encode($custom_error);
			die();
			//ErreurLog($Erreur);
		}

		if (!isset($Erreur)) {
			if (move_uploaded_file($_FILES['document']['tmp_name'], $repInt.$Hash.$ext_origin)==false) {
				//$Erreur="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				$custom_error['jquery-upload-file-error']="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				echo json_encode($custom_error);
				die();
				//ErreurLog($Erreur);
			}
			else {
				$Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Document (libele, lien, type) VALUES(:libele, :lien, :type)");
				$Insert->BindParam(":libele", $libele, PDO::PARAM_STR);
				$Insert->BindParam(":type", $Type, PDO::PARAM_STR);
				$Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
				$Insert->execute();
	
				if (!$Insert) {
					//$Erreur="Erreur serveur, veuillez réessayer ultèrieurement !";
					$custom_error['jquery-upload-file-error']="Erreur serveur, veuillez réessayer ultèrieurement !";
					echo json_encode($custom_error);
					die();
					//ErreurLog($Erreur);
				}
				else  {     
					$ret[]= $chemin;
					echo json_encode($ret);
					//header("location:".$Home."/Admin/Document/?valid=".urlencode($Valid));
				}
			}
		}
	}
	if (in_array($ext_origin, $ext5)) {
		$Code=md5(uniqid(rand(), true));
		$Hash=substr($Code, 0, 8);
		$taille_max="20000000";
		$repInt=$_SERVER['DOCUMENT_ROOT']."/lib/pps/";
		$repExt=$Home."/lib/pps/";
		$Type="PPS";
		$Lien=$repExt.$Hash.$ext_origin;

		if (!file_exists($repInt)) {
			mkdir($repInt, 0777);
		}

		if($taille_origin>$taille_max) {
			$custom_error['jquery-upload-file-error']="Fichier trop volumineux, il ne doit dépassé les 20Mo";
			echo json_encode($custom_error);
			die();
		}

		if (!isset($Erreur)) {
			if (move_uploaded_file($_FILES['document']['tmp_name'], $repInt.$Hash.$ext_origin)==false) {
				$custom_error['jquery-upload-file-error']="Erreur de téléchargement, veuillez réassayer ultèrieurement";
				echo json_encode($custom_error);
				die();
			}
			else {
				$Insert=$cnx->prepare("INSERT INTO ".$Prefix."neuro_Document (libele, lien, type) VALUES(:libele, :lien, :type)");
				$Insert->BindParam(":libele", $libele, PDO::PARAM_STR);
				$Insert->BindParam(":type", $Type, PDO::PARAM_STR);
				$Insert->BindParam(":lien", $Lien, PDO::PARAM_STR);
				$Insert->execute();

				if (!$Insert) {
					$custom_error['jquery-upload-file-error']="Erreur serveur, veuillez réessayer ultèrieurement !";
					echo json_encode($custom_error);
					die();
				}
				else  {     
					$ret[]= $chemin;
					echo json_encode($ret);
				}
			}
		}
	}
}
?>