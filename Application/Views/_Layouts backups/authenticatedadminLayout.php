<?php

use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;

$usuario = (isset(Session::Instance()->usuario)) ? Session::Instance()->usuario : null;
    
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?=SystemConfig::Instance()->ApplicationName?> | <?=SystemConfig::Instance()->ApplicationDescription?></title>

    
    <!-- <link rel="shortcut icon" href="/favicon.ico" type="image/x-icon">
    <link rel="icon" href="/favicon.ico" type="image/x-icon"> -->

    @@RenderBundle(zingchartJS)
    
    <script> 
        zingchart.MODULESDIR = "/vendor/zingchart/modules/";		
    </script>

    <!-- FONTS -->
    @@RenderBundle(appFonts)

    <!-- Vendor styles -->
    @@RenderBundle(vendorCSS)
    @@RenderBundle(ThemeCSS)    
    @@RenderBundle(nodeModulesCSS)
    @@RenderBundle(appCSS)
    
    @@RenderViewStyle()
</head>
<body>

    <!--[if lt IE 7]>
    <p class="alert alert-danger">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->
    <!-- Header -->
    <div id="header">
        @@RenderPartial(_Layouts/header)
    </div>

    <!-- Navigation -->
    <aside id="menu">
        @@RenderPartial(_Layouts/navigationadmin)
    </aside>

    <!-- Main view  -->
    <div id="wrapper" class="login-background">

        @@RenderContent()

        <!-- Right Sidebar -->
        @@RenderPartial(_Layouts/rightsidebar)

        <!-- Footer-->
        @@RenderPartial(_Layouts/footer)
    </div>
    
    @@RenderBundle(jqueryJS)
    @@RenderBundle(bootstrapJS)
    @@RenderBundle(commonJS)
    @@RenderBundle(appJS)
    @@RenderBundle(ServiceJS)
    @@RenderBundle(nodeModulesJS)
    @@RenderBundle(chartJS)
    @@RenderBundle(rightsideJS)

    @@RenderViewScript()

    <!-- Localization-->
    @@RenderSystem(localization)

</body>
</html>
