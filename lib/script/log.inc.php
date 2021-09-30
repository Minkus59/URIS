<?php
function ErreurLog($ErreurVar) {
    $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
    $ErreurLogFich=$ErreurLogDoc."erreurlog.txt";
    $Ip=$_SERVER['REMOTE_ADDR'];

    if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
        mkdir($ErreurLogDoc, 0777);
    }

    $Ouverture=fopen($ErreurLogFich, "a+");
    //Si une session existe
    if (isset($_SESSION['NeuroClient'])) { 
        $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    // si aucune session
    else {
        $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    fclose();
}

function ErreurLogMail($ErreurVar) {
    $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
    $ErreurLogFich=$ErreurLogDoc."erreurlogMail.txt";
    $Ip=$_SERVER['REMOTE_ADDR'];

    if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
        mkdir($ErreurLogDoc, 0777);
    }

    $Ouverture=fopen($ErreurLogFich, "a+");
    //Si une session existe
    if (isset($_SESSION['NeuroClient'])) { 
        $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    // si aucune session
    else {
        $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    fclose();
}

function LogMail($ErreurVar) {
    $ErreurLogDoc=$_SERVER['DOCUMENT_ROOT']."/lib/Log/";
    $ErreurLogFich=$ErreurLogDoc."logMail.txt";
    $Ip=$_SERVER['REMOTE_ADDR'];

    if(!file_exists($_SERVER['DOCUMENT_ROOT']."/lib/Log/")) {
        mkdir($ErreurLogDoc, 0777);
    }

    $Ouverture=fopen($ErreurLogFich, "a+");
    //Si une session existe
    if (isset($_SESSION['NeuroClient'])) { 
        $Ecriture=fwrite($Ouverture, $SessionClient." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    // si aucune session
    else {
        $Ecriture=fwrite($Ouverture, $Ip." ; ".$ErreurVar." ; ".date("d-m-y / G:i:s", time())."\n");
    }
    fclose();
}

?>