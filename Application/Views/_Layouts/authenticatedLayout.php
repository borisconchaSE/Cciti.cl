<?php

use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;

$usuario = Session::Instance()->usuario;
$AppName     =   SystemConfig::Instance()->ApplicationName ?: "App";
    
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

    <!-- FONTS -->
    @@RenderBundle(appFonts)
    <!-- Vendor styles -->
    @@RenderBundle(sexytabsCSS)
    @@RenderBundle(vendorCSS)
    @@RenderBundle(ThemeCSS)
    @@RenderBundle(nodeModulesCSS)
    @@RenderBundle(appCSS)
    @@RenderViewStyle()
    @@RenderBundle(jqueryJS)
</head>
<body class="pace-running"> 
        <div id="layout-wrapper">
            @@RenderPartial(_Layouts/header) 
            @@RenderPartial(_Layouts/navigation) 
            <div class="main-content">
                <div class="page-content"> 
                    <div class="container-fluid"> 
                        @@RenderContent()   
                    </div>
                </div> 
             
                <footer class="footer">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-sm-6">
                                <script>document.write(new Date().getFullYear())</script> © <?= $AppName ?>.
                            </div>
                            <div class="col-sm-6">
                                <div class="text-sm-end d-none d-sm-block">
                                    Gerencia de Transformación Digital y Clientes
                                </div>
                            </div>
                        </div>
                    </div>
                </footer>

            </div>

        </div>
</body>
<footer>
    


    @@RenderBundle(bootstrapJS)
    @@RenderBundle(commonJS)
    @@RenderBundle(ServiceJS)
    @@RenderBundle(ThemeJS) 
    @@RenderBundle(appJS)
    @@RenderBundle(nodeModulesJS) 
    @@RenderBundle(amChart5JS)

    @@RenderPartial(_Layouts/footer)

    @@RenderViewScript()
 
</footer> 
 
</html>
