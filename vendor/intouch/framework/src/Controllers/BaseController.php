<?php

namespace Intouch\Framework\Controllers;

use Application\BLL\DataTransferObjects\WS\StandardJsonResultWS;
use Intouch\Framework\Configuration\BundleConfig;
use Intouch\Framework\Assets\IAssetManagerFactory;
use Intouch\Framework\Assets\IAssetManager;
use Intouch\Framework\Assets\Entities\ImageResource;
use Intouch\Framework\Bundles\Bundle;
use Intouch\Framework\Mensajes\Mensaje;
use Karriere\JsonDecoder\JsonDecoder;

class BaseController {

    private IAssetManager $imageReader;
    private IAssetManager $assetReader;
    private string $controller = '';

    public $Idioma = DEFAULT_LANGUAGE;
    public $Producto = DEFAULT_PRODUCT;
    public $Locale = DEFAULT_PRODUCT . '_' . DEFAULT_LANGUAGE;


    public function __construct(protected IAssetManagerFactory $assetManagerFactory, $forcedController = '') {
            $this->imageReader = $assetManagerFactory->GetImageReaderAM();
            $this->assetReader = $assetManagerFactory->GetImageReaderAM();
            $this->controller = $forcedController;
    }
    
    public function Redirect(string $uri) {
        header("Location: $uri");
        die();
    }

    public static function GetPathExtension($uri) {

        $path_parts = pathinfo($uri);

        return isset($path_parts['extension']) ? $path_parts['extension'] : '';
    }

    private function GetViewFileLocation($viewname) {

        if ($this->controller != '') {
            $controllerName = $this->controller;
        }
        else {
            $classFullName = str_replace('Application\\Controllers\\', '',  (new \ReflectionClass(get_called_class()))->getName());
            $controllerName = str_replace('\\', '/', str_replace('Controller', '', $classFullName));
        }

        $masterPath = VIEW_ROOT . '/' . $controllerName;

        return $masterPath . '/' . $viewname . 'View.php';
    }

    public function RenderView($view, $data = null) {
        $viewFilePath = $this->GetViewFileLocation($view);
        return $this->RenderFile($viewFilePath, $data);
    }

    private function RenderFile($file, $data = null) {

        if (file_exists($file)) {
            ob_start();
            include( $file );
            $content = ob_get_contents();
            ob_end_clean();
        }
        else {
            return "<div><strong>No existe la vista especificada (".$file.")</div>";
        }

        // Obtener el layout si es que existe

        // La funcion "ObtenerRenderSections" devuelve un arreglo de pares "Id => StringTotal"
        // Ej: "guest" => "@@Layout(guest)"
        $layouts = $this->ObtenerRenderSections($content, '@@Layout');
        
        if (count($layouts) > 0) {
            // Solo se considerará el primer layout
            if (count($layouts) > 1) {
                return "<div><strong>Error de definici&oacute;n</strong> se ha definido más de 1 layout para esta vista (".$view.")</div>";
            }
            else {
                // Obtener el layout
                foreach($layouts as $layout=>$searchString) {

                    $layoutFilePath = LAYOUT_ROOT . '/' . $layout . 'Layout.php';
                    $layoutContent = "";
                    
                    if (isset($layout) && $layout != "") {

                        // Ver si existe el archivo
                        if (file_exists($layoutFilePath)) {
                            ob_start();
                            include( $layoutFilePath );
                            $layoutContent = ob_get_contents();
                            ob_end_clean();
                        }
                        else {
                            return "<div><strong>No existe el layout (".$layoutFilePath.") especificado en esta vista (".$file.")</div>";
                        }
                    }

                    $content = $this->RenderContent($content, $layoutContent, $searchString);

                    // Solo se debe ejecutar 1 vez.
                    break;
                }

            }
        }

        // Renderizar las secciones
        $content = $this->RenderSections($content, $data);

        // Renderizar las cadenas de texto
        // Ejemplo:  @@{MsgWrongPassword} se convierte en "Wrong username or password" (sin las comillas)

        //$prueba =  $this->RenderStrings($content);
        $content = $this->RenderStrings($content);

        return $content;
    }


    public function RenderContent($content, $layoutContent = '', $searchString = '') {

        // Encontrar los bundles de la vista e inyectarlos en el layout
        // La funcion "ObtenerCadenasReemplazoBundles" devuelve un arreglo de pares "CadenaTotal" => "reemplazo"
        // Ej: '@@IncludeStyleBundle(estilosCss)' => '<link href="/styles/estilo.css" rel="stylesheet" type="text/css">'
        $renderStyleResults = $this->ObtenerCadenasReemplazoBundles($content, '@@IncludeStyleBundle');                    
        if (count($renderStyleResults) > 0) {

            $insertarStyle = "";
            foreach($renderStyleResults as $searchBundle=>$replaceBundle) {
                // Acumulamos el código para insertar en el layout
                $insertarStyle .= $replaceBundle;
                // Borramos la definición desde la vista (aquí no será útil)
                $content = \str_replace($searchBundle, '', $content);
            }

            // Buscamos en el layout dónde insertar el código
            // Debemos reemplazar la llave "@@RenderViewStyles()"
            // Reemplazamos en el layout el código acumulado
            if (isset($layoutContent))
                $layoutContent = \str_replace('@@RenderViewStyle()', $insertarStyle, $layoutContent);

        } else {
            // Si la vista no ha definido bundle de estilo, en el layout
            // debemos quitar la referencia que pueda existir
            if (isset($layoutContent))
                $layoutContent = \str_replace('@@RenderViewStyle()', '', $layoutContent);
        }

        // Ej: '@@IncludeScriptBundle(vistaJS)' => '<script src="/scrtips/vista.js">'
        $renderScriptResults = $this->ObtenerCadenasReemplazoBundles($content, '@@IncludeScriptBundle');                    
        if (count($renderScriptResults) > 0) {

            $insertarScript = "";
            foreach($renderScriptResults as $searchBundle=>$replaceBundle) {
                // Acumulamos el código para insertar en el layout
                $insertarScript .= $replaceBundle;
                // Borramos la definición desde la vista (aquí no será útil)
                $content = \str_replace($searchBundle, '', $content);
            }

            // Buscamos en el layout dónde insertar el código
            // Debemos reemplazar la llave "@@RenderViewScript()"
            // Reemplazamos en el layout el código acumulado
            if (isset($layoutContent))
                $layoutContent = \str_replace('@@RenderViewScript()', $insertarScript, $layoutContent);
        } else {
            // Si la vista no ha definido bundle de scripts, en el layout
            // debemos quitar la referencia que pueda existir
            if (isset($layoutContent))
                $layoutContent = \str_replace('@@RenderViewScript()', '', $layoutContent);
        }

        // Ej: '@@Src(projects/proj001.jpg)' => /images/projects/proj001.jpg o "aws.cloudfront.com/ffeoiu34987329827687ywiuewyfh"
        $renderAssetResults = $this->ObtenerReemplazoAssets($content, '@@Src');                    
        if (count($renderAssetResults) > 0) {
            $insertarScript = "";
            foreach($renderAssetResults as $search => $replace) {
                // Borramos la definición desde la vista (aquí no será útil)
                $content = \str_replace($search, $replace, $content);
            }
        }

        $renderAssetResults = $this->ObtenerReemplazoAssets($content, '@@Url');                    
        if (count($renderAssetResults) > 0) {
            $insertarScript = "";
            foreach($renderAssetResults as $search => $replace) {
                // Borramos la definición desde la vista (aquí no será útil)
                $content = \str_replace($search, $replace, $content);
            }
        }

        // Ejecutar render de vistas de sistema
        $renderSystem = $this->ObtenerRenderSections($content, '@@RenderSystem');

        foreach($renderSystem as $systemViewLocation=>$searchString) {
            // Obtener la seccion
            ob_start();
            include(__DIR__ . '/../SystemViews/' . $systemViewLocation . 'View.php');
            $systemView = ob_get_contents();
            ob_end_clean();

            // Reemplazar en la vista
            $content = \str_replace($searchString, $systemView, $content);
        }

        // Ahora, embebemos la vista dentro del layout
        if ($layoutContent != "") {
            $layoutContent = \str_replace('@@RenderContent()', $content, $layoutContent);
            $content = $layoutContent;
        }

        // Eliminamos la declaracion
        $content = \str_replace($searchString, '', $content);

        return $content;
    }

    public function RenderStrings($content) {

        $cadenas = $this->ObtenerRenderString($content);

        // Reemplazar las cadenas
        foreach ($cadenas as $key=>$cadena) {
            $content = str_replace($key, $cadena, $content);
        }

        return $content;
    }


    public function ObtenerPrefixSufix($messageId, $usaAlternativo = "") {
        return self::ObtenerPrefixSufixDo($messageId, $usaAlternativo, $this->Locale);
    }

    public static function ObtenerPrefixSufixDo($messageId, $usaAlternativo = "", $locale = "") {

        $prefix = "<span data-messageid='$messageId' data-locale='$locale' data-alternativo='$usaAlternativo' style='display:inline' class='message-content'>";
        $sufix = "</span>";

        // Revisar si está todo definido para este MSG
        if (Mensaje::MensajeDefinidoCompleto($messageId)) {
            $color = "success";
        }
        else {
            $color = "purple";
        }

        if ($GLOBALS['environment'] == "desarrollo") {
            $sufix = "</span><label data-messageid='$messageId' class='btn btn-$color btn-xs message-edit new' style='display:none; margin-left: 4px;'><i class='fas fa-edit' style='color: #ffffff; font-size: 11px !important;'></i></label>";
        }

        $prefix = str_replace("\r", '', str_replace("\n", '', $prefix));
        $sufix = str_replace("\r", '', str_replace("\n", '', $sufix));

        return [$prefix, $sufix];
    }

    private function RenderSections($content, $data = null) {
        // Identificar todos los @@RenderPartial y sustituirlos por el contenido
        $renderPartial = $this->ObtenerRenderSections($content, '@@RenderPartial');

        foreach($renderPartial as $partialViewLocation=>$searchString) {

            // Obtener la seccion
            $partialView = $this->RenderFile(VIEW_ROOT . '/' . $partialViewLocation . 'PartialView.php', $data);

            $partialView = $this->RenderContent($partialView);

            // ob_start();
            // include( VIEW_ROOT . '/' . $partialViewLocation . 'PartialView.php');
            // $partialView = ob_get_contents();
            // ob_end_clean();

            // Reemplazar en la vista
            $content = \str_replace($searchString, $partialView, $content);
        }

        
        // Ejecutar render de bundles
        $reemplazoBundles = $this->ObtenerCadenasReemplazoBundles($content, '@@RenderBundle');
        
        // Reemplazar los valores obtenidos en el contenido final
        foreach($reemplazoBundles as $searchString=>$replaceString) {
            $content = \str_replace($searchString, $replaceString, $content);
        }

        // Ejecutar render de vistas de systema
        $renderSystem = $this->ObtenerRenderSections($content, '@@RenderSystem');

        foreach($renderSystem as $systemViewLocation=>$searchString) {
            // Obtener la seccion
            ob_start();
            include(__DIR__ . '/../SystemViews/' . $systemViewLocation . 'View.php');
            $systemView = ob_get_contents();
            ob_end_clean();

            // Reemplazar en la vista
            $content = \str_replace($searchString, $systemView, $content);
        }
        

        return $content;
    }

    private function ObtenerRenderSections($content, $key = "@@RenderPartial") {

        $offset = strlen($key) + 1;

        $renderSection = array();        
        $pos = 0;
        $len = strlen($content);   
        
        while ($pos < $len) {
            // Ver la siguiente posicion
            $pos = strpos($content, $key."(", $pos);
        
            if ($pos === false)
                break;
        
            // se encontro la cadena
            // averiguar donde se cierra el parentesis
            $posBracket = strpos($content, ")", $pos + 1);
        
            if (!$posBracket) {
                // no se encontro el cierre del bracket
                $pos++;
            }
            else if ($posBracket - $pos > 64) { // valores mayores a 64 caracteres son sospechosos de un mal cierre de bracket en el codigo
                //echo "Bracket mal cerrado";
                $pos++;
            }
            else {
                // perfecto!
                // obtenemos el contenido
                $controller = substr($content, $pos + $offset, $posBracket - $pos - $offset);        
                $renderSection[$controller] = $key.'('.$controller.')';
                $pos = $posBracket; 
            } 
        }
        
        return $renderSection;
    }

    private function ObtenerRenderString ($content, $key = "@@") {
        return self::RenderMensaje($content, $key, $this->Idioma, $this->Locale);
    }

    public static function RenderMensaje($content, $key = "@@", $idioma = '', $locale = '') {

        $offset = strlen($key) + 1;

        $renderString = array();
        $pos = 0;
        $len = strlen($content);   
        
        while ($pos < $len) {
            // Ver la siguiente posicion
            $pos = strpos($content, $key.'{', $pos);
        
            if ($pos === false)
                break;
        
            // se encontro la cadena
            // averiguar donde se cierra la llave "}"
            $posBracket = strpos($content, "}", $pos + 1);
        
            if (!$posBracket) {
                // no se encontro el cierre de la llave
                $pos++;
            }
            else if ($posBracket - $pos > 256) { // valores mayores a 256 caracteres son sospechosos de un mal cierre de bracket en el codigo
                //echo "Bracket mal cerrado";
                $pos++;
            }
            else {
                // perfecto!
                
                // obtenemos el contenido
                $contenido = substr($content, $pos + $offset, $posBracket - $pos - $offset);                
                $messageId = $contenido;

                // buscar si viene texto alternativo
                if (strpos($contenido, ",") !== false) {
                    $split = explode(",", $contenido);
                    $messageId = $split[0];
                    $alternativo = trim($split[1]);
                }
                else {
                    //$messageId = $contenido;
                    $alternativo = '';
                }                

                // buscar el $messageId en el sistema de idiomas
                $entry = _msg($messageId, $idioma);

                // Si el ambiente es de desarrollo, agregamos los ganchos para modificación en línea
                $mensaje = "";
                $usaAlternativo = "";
                if ( (isset($entry) && $entry != '') || (isset($alternativo) && $alternativo != '')) {
                                        
                    if (isset($entry) && trim($entry) != "") {
                        $textoMostrar = trim($entry);
                    }
                    else {
                        $textoMostrar = $alternativo;
                        $usaAlternativo = $alternativo;
                    }                        

                    // $parts = $this->ObtenerPrefixSufix($messageId, $usaAlternativo);
                    // $mensaje = $parts[0] . $textoMostrar . $parts[1];
                }
                else {
                    $textoMostrar = 'MsgID not found';
                    // $parts = $this->ObtenerPrefixSufix($messageId, $alternativo);
                    // $mensaje = $parts[0] . 'MsgID not found' . $parts[1];
                }

                $parts = self::ObtenerPrefixSufixDo($messageId, $usaAlternativo, $locale);
                $mensaje = $parts[0] . $textoMostrar . $parts[1];

                $renderString[$key.'{'.$messageId . ($alternativo != '' ? ', ' . $alternativo : '' ) .'}'] = $mensaje;
                $pos = $posBracket; 
            } 
        }
        
        return $renderString;
    }

    private function ObtenerReemplazoAssets($content, $key) {

        $result = array();

        $renderAssets = $this->ObtenerRenderSections($content, $key);

        foreach ($renderAssets as $reemplazo => $origen) {

            // Ver si vienen mas datos
            $imagen = explode(',', $reemplazo);
            $file = trim($imagen[0]);
            $style = '';
            $htmlId = '';
            $htmlName = '';

            $resource = new ImageResource(id: '', name:'');
            $resource->location = "$file";

            if (strtolower($key) == '@@src') {
                // recorrer cualquier propiedad que pudiera venir
                for ($idx=1; $idx < count($imagen); $idx++) {
                    $prop = explode('=', $imagen[$idx]);

                    if (isset($prop[0]) && isset($prop[1])) {

                        $name = trim(strtolower($prop[0])); 
                        $value = trim(strtolower($prop[1]));

                        switch($name) {
                            case "id":
                                $resource->id = $value;
                                $htmlId  = $value;
                                break;
                            case "name":
                                $resource->name = $value;
                                $htmlName  = $value;
                                break;
                            case "width":
                                if (is_numeric($value))
                                    $resource->width = trim($prop[1]) * 1;

                                $style .= "$name: " . $value . "px; ";
                                break;
                            case "height":
                                if (is_numeric($value))
                                    $resource->height = trim($prop[1]) * 1;

                                $style .= "$name: " . $value . "px; ";
                                break;
                        }
                    }
                }
            }

            switch(strtolower($key)) {
                case '@@src': // img src                    
                    $result[$origen] = 
                        "src='" . $this->imageReader->GetAssetUri($resource)->uri . "'"
                                . (($htmlId != '') ? " id='$htmlId'" : "")
                                . (($htmlName != '') ? " name='$htmlName'" : "")
                                . (($style != '') ? " style='$style'" : "");
                    break;
                case '@@url': // img url
                    $result[$origen] = $this->imageReader->GetAssetUri($resource)->uri;
                    break;
            }
        }

        return $result;
    }

    private function ObtenerCadenasReemplazoBundles($content, $key = "@@RenderBundle") {

        // Obtenermos todos los bundles definidos en la configuracion de la aplicacion
        $bundles = BundleConfig::Instance();

        // Ver si existe versionamiento a aplicar (aplica en producción...)
        if (isset($GLOBALS['app_version'])) {
            $version = $GLOBALS['app_version'];
        }
        else {
            $version = '';
        }

        // El resultado a entregar
        // array ["CadenaDeBusqueda" => "Archivos",]
        // ej: ["@@RenderBundle(jqueryJS)" => "<link src='/vendor/jquery/jquery.min.js' />"]
        $result = array();

        // Buscamos todos los bundles (o la cadena que se haya solicitado, @@RenderBundle, @@Define... etc)
        $renderBundles = $this->ObtenerRenderSections($content, $key);

        // Recorremos todos los bundles encontrados
        foreach($renderBundles as $idBundle=>$searchString) {
            
            // Buscar el bundle
            if (isset($bundles)) {
                if (isset($bundles[$idBundle])) {
                    $bundle = $bundles[$idBundle];
                    $replaceString = "";

                    // Recorrer los sources
                    foreach($bundle->Sources as $source) {

                        // reconocer patrones de busqueda de archivos
                        $found = [];
                        $patron = $source['File'];
                        $relative = $bundle->BundleLocation . (  ($source['Folder'] != '') ? '/' . $source['Folder'] : '');
                        $carpeta = SITE_ROOT . '/public/' . $relative;

                        // buscar archivos coincidentes

                        if ($version != '' && $bundle->Versionado) {
                            $matches = glob($this->AgregarVersion($carpeta . '/' . $patron, $version));
                        }
                        else {
                            $matches = glob($carpeta . '/' . $patron);
                        }

                        if (is_array ($matches)) {
                            foreach ($matches as $path) {
                                if (is_file($path)) {
                                    $filename = basename($path);
                                    if ($filename != '.' && $filename != '..' && $filename != '') {
                                        if (!isset($found[$filename])) {
                                            $found[$filename] = SITE_URL . '/' . $relative . '/' . $filename;
                                        }
                                    }
                                }
                            }
                        }

                        foreach($found as $file => $filePath) {
                            
                            // agregar version, si aplica
                            // if ($bundle->Versionado) {
                            //     if ($version != '') {
                            //         $filePath = $this->AgregarVersion($filePath, $version);
                            //     }
                            // }

                            // Generar la declaracion de source
                            $filePath = $this->ObtenerFileLink($filePath, $bundle->Tipo, isset($source['Media']) ? $source['Media'] : null);
                            $replaceString .= $filePath;
                        }

                        // $filePath = '/' . $bundle->BundleLocation . (  ($source['Folder'] != '') ? '/' . $source['Folder'] : '') . '/' . $source['File'];

                        // // agregar version, si aplica
                        // if ($bundle->Versionado) {
                        //     if ($version != '') {
                        //         $filePath = $this->AgregarVersion($filePath, $version);
                        //     }
                        // }                        

                        // // Generar la declaracion de source
                        // $filePath = $this->ObtenerFileLink($filePath, $bundle->Tipo);                        
                        // $replaceString .= $filePath;
                    }
                    $result[$searchString] = $replaceString;
                    //$content = \str_replace($searchString, $replaceString, $content);
                }
            } else {
                // Reemplazar la referencia en la vista
                $result[$searchString] = "<div><strong>BUNDLE NO ENCONTRADO :</strong> ".$idBundle."</div>";
            }
        }

        return $result;
    }

    private function AgregarVersion($archivo, $version) {

        $path_parts = pathinfo($archivo);

        $dir = $path_parts['dirname'];
        $filename = $path_parts['filename'];
        $extension = $path_parts['extension'];

        return $dir.'/'.$filename.'.'.$version.'.'.$extension;
    }

    private function ObtenerFileLink($filePath, $tipo, $media = null) {

        switch ($tipo) {
            case "typescript":
                return '<script src="'.$filePath.'"></script>' . "\n";
            case "javascript":
                return '<script src="'.$filePath.'"></script>' . "\n";
            case "css":
                return '<link rel="stylesheet" href="'.$filePath.'" ' . ( (isset($media) ? ('media="' . $media . '"') : '') ) . ' />' . "\n";
            case "font":
                return '<link href="'.$filePath.'" rel="stylesheet" type="text/css">' . "\n";
            default:
                return $filePath;
        }
    }
/*
    public function RenderWsResult($idError = 0, $msgError = '', $msgFriendly = '', $data = null) {
        $wsResult = new StandardJsonResultWS();

        $wsResult->IdError = $idError;
        $wsResult->MsgError = $msgError;
        $wsResult->MsgFriendly = $msgFriendly;
        $wsResult->Data = $data;

        return $wsResult;
    }

    public function EncodeWsResult($wsResult) {
        return  json_encode($wsResult, JSON_UNESCAPED_UNICODE);
    }

    public function RenderJsonResult($idError = 0, $msgError = '', $msgFriendly = '', $data = null) {
        $json = new StandardJsonResultWS();

        $json->IdError = $idError;
        $json->MsgError = $msgError;
        $json->MsgFriendly = $msgFriendly;
        $json->Data = $data;

        $encoded = json_encode($json, JSON_UNESCAPED_UNICODE);

        return $encoded;
    }

    public function DecodeJsonParameter($jsonParameter, $class) {
        $jsonDecoder = new JsonDecoder();

        return $jsonDecoder->decode($jsonParameter, $class);
    }
    */

    public function GetRequest($name = '') {
        if (isset($_REQUEST[$name])) {
            return $_REQUEST[$name];
        }
        else if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        else if (isset($_GET[$name])) {
            return $_GET[$name];
        }
        else {            
            return file_get_contents('php://input');
        }
    }

    public function GetObjectParams() {

        $objparams = $_GET;

        if (isset($objparams)) {
            return (object) $objparams;
        }
        else {
            return null;
        }
    }

    public function DecodeJsonRequest($requestId, $class) {
        $jsonParameter = $this->GetRequest($requestId); // $_REQUEST[$requestId];

        if (isset($jsonParameter)) {
            $jsonDecoder = new JsonDecoder();
            return $jsonDecoder->decode($jsonParameter, $class);
        }
        else {
            return null;
        }
    }

    public function DecodeJsonParameterArray($jsonParameter, $class) {
        $jsonDecoder = new JsonDecoder();

        return $jsonDecoder->decodeMultiple($jsonParameter, $class);
    }

    protected function DisplayImage($image) {

        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: January 01, 2013');
        header('Pragma: no-cache');
        header("Content-Type: image/jpg");

        echo $image;
    }

    protected function DownloadXml($file, $filename) {

        header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');
        header('Expires: January 01, 2013');
        header('Pragma: no-cache');
        header('Content-Disposition: attachment; filename=' . $filename);
        header("Content-Type: application/xml");

        echo $file;
    }

    protected function DownloadPdf($file, $filename) {

        //header('Cache-Control: no-cache, no-store, max-age=0, must-revalidate');        
       // header('Pragma: no-cache');
        //header('Content-Disposition: attachment; filename=' . $filename);
        header("Content-Description: File Transfer"); 
        header("Content-Type: application/octet-stream"); 
        header('Content-Type: application/pdf');

        
        echo $file;
        
    }

}