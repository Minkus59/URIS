<body>
<center>
    
<header>
<div id="Center">
    <div id="sousLogo">
    <div id="Logo">   
        <a href='<?php echo $ParamLogoHeader->lien; ?>'><img src="<?php echo $ParamLogoHeader->logo; ?>"/></a>
    </div>
    <div id="Region">   
        <img src="<?php echo $ParamLogoRegion->logo; ?>"/>
    </div>
    </div>

    <div id="Carouselle">
        <div class="basic-slider-container">
        <span id="basic-slider-previous" class="basic-slider-previous">
        <
        </span>
        <span id="basic-slider-next" class="basic-slider-next">
        >
        </span>
    
        <ul id="basic-slider" class="basic-slider">
            <?php 
            $SelectCarousel=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_Carousel WHERE statue='1' ORDER BY position ASC");
            $SelectCarousel->execute();

            while ($ImageCarousel=$SelectCarousel->fetch(PDO::FETCH_OBJ)) { ?>
            <li><div class="basic-slider-item"><img src="<?php echo $ImageCarousel->lien; ?>" alt="<?php echo $ImageCarousel->description; ?>"></div></li>
        <?php } ?>
        </ul>
        </div>
        
        <div id="basic-slider-indicators" class="basic-slider-indicators">
        </div>
    </div>

    <aside class="FlashInfo">

        <H1>Flash Info</H1><BR />
        <?php
        $SelectFlash=$cnx->prepare("SELECT * FROM ".$Prefix."neuro_FlashInfo WHERE statue='1' ORDER BY position ASC");
        $SelectFlash->execute();

        while ($Flash=$SelectFlash->fetch(PDO::FETCH_OBJ)) { ?>
            <div id="Info">
                <?php echo $Flash->description; ?>
            </div>
        <?php } ?>
    </aside>
    </div>

<script src="<?php echo $Home; ?>/lib/js/ga-basic-slider-0.0.4-min.js"></script> 
<script src="<?php echo $Home; ?>/lib/js/slider.js"></script> 
</header>