<!DOCTYPE html>
<html>
<?php

use Intouch\Framework\Configuration\SystemConfig;

?>
<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- Page title -->
    <title><?=SystemConfig::Instance()->ApplicationName?> | <?=SystemConfig::Instance()->ApplicationDescription?></title>

    <!-- Place favicon.ico and apple-touch-icon.png in the root directory -->
    <!--<link rel="shortcut icon" type="image/ico" href="favicon.ico" />-->

    <!-- FONTS -->
    @@RenderBundle(appFonts)

    <!-- Vendor styles -->
    @@RenderBundle(vendorCSS)
    @@RenderBundle(ThemeCSS)
    @@RenderBundle(nodeModulesCSS)
    @@RenderBundle(appCSS)

    @@RenderViewStyle()

</head>

<body class="pace-done">

    <!--[if lt IE 7]>
<p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
<![endif]-->
 

    <?php
        if ($GLOBALS['environment'] == "desarrollo") {
    ?>
                            
            <a href="#" onclick="return false;">
                <div class='btn btn-warning btn-md pull-left toggle-message-edit' style="margin: 15px;">Translate</div>
            </a>
    <?php
        }
    ?>

    @@RenderContent()

    @@RenderBundle(jqueryJS)
    @@RenderBundle(bootstrapJS)
    @@RenderBundle(commonJS)

    <!-- Localization-->
    @@RenderSystem(localization)
    @@RenderBundle(appJS)
    @@RenderBundle(ServiceJS)
    @@RenderBundle(nodeModulesJS)

    @@RenderViewScript()

</body>

</html>