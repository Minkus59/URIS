<nav>
<div id="NavSearch">
<form name="FormRecherche" action="/Recherche/" method="POST">
<input class="TextSearch" name="search" type="text" placeholder="Rechercher"/>
<input class="ButtonSearch" type="submit" name="Rechercher" value=""/>
</form>
</div>
    <label for="NavButton" class="LabelNavButton"></label>
    <input class="NavButton" id="NavButton" type="checkbox"/>

    <ul class='sub'>
        <li <?php if ($PageActu=="/") { echo "class='Up'"; } ?>><a href="<?php echo $Home; ?>">Accueil</a></li>
        <?php
        while ($Page=$SelectPageActif->fetch(PDO::FETCH_OBJ)) {
            $SelectSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
            $SelectSousMenu->bindParam(':parrin', $Page->lien, PDO::PARAM_STR);
            $SelectSousMenu->execute();
            $CountSousMenu=$SelectSousMenu->rowCount();
            ?>
            <li <?php if ($PageActu==$Page->lien) { echo "class='Up'"; } ?>>
            <a href="<?php echo $Home.$Page->lien; ?>"><?php echo $Page->libele; ?></a>
            <?php if ($CountSousMenu>0) { echo '<label for="NavMenu'.$Page->id.'" class="LabelNavMenu"></label><input class="NavMenu" id="NavMenu'.$Page->id.'" type="checkbox"/>'; } ?>

            <?php 
            if ($CountSousMenu>0) {
                echo "<ul class='niveau2'>";
                while ($SousMenu=$SelectSousMenu->fetch(PDO::FETCH_OBJ)) { 
                    $SelectSousSousMenu=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                    $SelectSousSousMenu->bindParam(':parrin', $SousMenu->lien, PDO::PARAM_STR);
                    $SelectSousSousMenu->execute();
                    $CountSousSousMenu=$SelectSousSousMenu->rowCount();
                    ?>
                    <li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>>
                    <a href="<?php echo $Home.$SousMenu->lien; ?>"><?php echo $SousMenu->libele; ?></a>
                    <?php if ($CountSousSousMenu>0) { echo '<label for="NavMenu'.$SousMenu->id.'" class="LabelNavMenu"></label><input class="NavMenu" id="NavMenu'.$SousMenu->id.'" type="checkbox"/>'; } ?>

                    <?php 
                    if ($CountSousSousMenu>0) {
                        echo "<ul class='niveau3'>";
                        while ($SousSousMenu=$SelectSousSousMenu->fetch(PDO::FETCH_OBJ)) { ?>
                            <li <?php if ($PageActu==$SousSousMenu->lien) { echo "class='Up'"; } ?>><a href="<?php echo $Home.$SousSousMenu->lien; ?>"><?php echo $SousSousMenu->libele ?></a></li>
                        <?php
                        }
                    echo "</ul>";
                    }
                    ?></li>
                    <?php
                }
                echo "</ul>";
            }
        }
        ?></li>        
    </ul>


    <div id="NavImage"><a href="<?php echo $ParamLogoNav->lien; ?>"><img src="<?php echo $ParamLogoNav->logo; ?>"/></a></div>
</nav>