<footer>
    <div id="Center">
        <div id="Cadre1">  
            <a href='<?php echo $ParamLogoFooter->lien; ?>'><img src="<?php echo $ParamLogoFooter->logo; ?>"/></a>
        </div>
    
        <div id="Cadre2"> 
        <H3>Liens utiles</H3>
        <ul>
            <li><a href="<?php echo $Home; ?>">Accueil</a></li>
            <li><a href="<?php echo $Home; ?>/Mentions-legales/">Mentions-légales</a></li>
            <BR /><BR />
            <?php 
            $SelectSocial=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Social WHERE statue='1' ORDER BY id ASC");
            $SelectSocial->bindParam(':parrin', $PageFooter->lien, PDO::PARAM_STR);
            $SelectSocial->execute();
            
            while ($LienSocial=$SelectSocial->fetch(PDO::FETCH_OBJ)) { ?>
                <a href='<?php echo $LienSocial->lien; ?>'><img src="<?php echo $LienSocial->logo; ?>"/></a>
            <?php } ?>
        </ul>
        </div>

        <div id="Cadre3">  
            <H3>Nos Services</H3>
        <ul>
            <?php
            while ($PageFooter1=$SelectPageActifFooter1->fetch(PDO::FETCH_OBJ)) {
            ?>
                <li><a href="<?php echo $Home.$PageFooter1->lien ?>"><?php echo $PageFooter1->libele ?></a>

                <?php 
                $SelectSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                $SelectSousMenuFooter->bindParam(':parrin', $PageFooter1->lien, PDO::PARAM_STR);
                $SelectSousMenuFooter->execute();
                $CountSousMenu=$SelectSousMenuFooter->rowCount();

                if ($CountSousMenu>0) {
                    echo "<ul>";
                    while ($SousMenu=$SelectSousMenuFooter->fetch(PDO::FETCH_OBJ)) { ?>
                        <li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><a href="<?php echo $Home.$SousMenu->lien ?>"><?php echo $SousMenu->libele ?></a>

                        <?php 
                        $SelectSousSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                        $SelectSousSousMenuFooter->bindParam(':parrin', $SousMenu->lien, PDO::PARAM_STR);
                        $SelectSousSousMenuFooter->execute();
                        $CountSousMenu=$SelectSousSousMenuFooter->rowCount();

                        if ($CountSousMenu>0) {
                            echo "<ul>";
                            while ($SousSousMenu=$SelectSousSousMenuFooter->fetch(PDO::FETCH_OBJ)) { ?>
                                    <li <?php if ($PageActu==$SousSousMenu->lien) { echo "class='Up'"; } ?>><a href="<?php echo $Home.$SousSousMenu->lien ?>"><?php echo $SousSousMenu->libele ?></a></li><?php
                            }
                            echo "</ul>";
                        }
                    } ?></li>
                    <?php
                    echo "</ul>";
                } 
            }
            ?></li>
        </ul>
        </div>
        <div id="Cadre4">  
        <ul>
            <?php
            while ($PageFooter2=$SelectPageActifFooter2->fetch(PDO::FETCH_OBJ)) {
            ?>
                <li><a href="<?php echo $Home.$PageFooter2->lien ?>"><?php echo $PageFooter2->libele ?></a>

                <?php 
                $SelectSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                $SelectSousMenuFooter->bindParam(':parrin', $PageFooter2->lien, PDO::PARAM_STR);
                $SelectSousMenuFooter->execute();
                $CountSousMenu=$SelectSousMenuFooter->rowCount();

                if ($CountSousMenu>0) {
                    echo "<ul>";
                    while ($SousMenu=$SelectSousMenuFooter->fetch(PDO::FETCH_OBJ)) { ?>
                        <li <?php if ($PageActu==$SousMenu->lien) { echo "class='Up'"; } ?>><a href="<?php echo $Home.$SousMenu->lien ?>"><?php echo $SousMenu->libele ?></a>

                        <?php 
                        $SelectSousSousMenuFooter=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Page WHERE parrin=:parrin AND sous_menu='1' AND statue='1' ORDER BY position ASC");
                        $SelectSousSousMenuFooter->bindParam(':parrin', $SousMenu->lien, PDO::PARAM_STR);
                        $SelectSousSousMenuFooter->execute();
                        $CountSousMenu=$SelectSousSousMenuFooter->rowCount();

                        if ($CountSousMenu>0) {
                            echo "<ul>";
                            while ($SousSousMenu=$SelectSousSousMenuFooter->fetch(PDO::FETCH_OBJ)) { ?>
                                    <li <?php if ($PageActu==$SousSousMenu->lien) { echo "class='Up'"; } ?>><a href="<?php echo $Home.$SousSousMenu->lien ?>"><?php echo $SousSousMenu->libele ?></a></li><?php
                            }
                            echo "</ul>";
                        }
                    } ?></li>
                    <?php
                    echo "</ul>";
                } 
            }
            ?></li>
        </ul>
        </div>

    </div>
</footer>

</center>
</body>

</html>