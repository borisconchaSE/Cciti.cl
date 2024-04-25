<?php
 
// include __DIR__ . '/../vendor/autoload.php';
// include __DIR__ . '/../autoload.php';
// include_once __DIR__ . '/../func.php';
// include_once __DIR__ . '/../autoload_vars.php';
// include_once __DIR__ . '/../version.php';

use Intouch\Framework\Cache\RedisSvc;
use Intouch\Framework\Dates\Date;

include __DIR__ . '/../vendor/autoload.php';
include __DIR__ . '/../autoload.php';
include_once __DIR__ . '/../vendor/intouch/framework/src/Init/func.php';
include_once __DIR__ . '/../start.php';
include_once __DIR__ . '/../version.php';

RedisSvc::FlushAll();

//exec('redis-cli KEYS "*" | grep -v "PHPREDIS_SESSION:" | xargs redis-cli DEL');

use Application\BLL\BusinessObjects\Core\AuditoriaNavegacionBO;
use Application\BLL\Document\AvatarDocument;
use Application\BLL\Document\Definitions\AvatarDocumentDefinition;
use Application\BLL\Document\Definitions\DocumentTypeEnum;
use Application\BLL\Document\Definitions\FotoDocumentDefinition;
use Application\BLL\Document\Definitions\LogoDocumentDefinition;
use Application\BLL\Document\Definitions\PlanDocumentDefinition;
use Application\BLL\Document\Definitions\ReportDocumentDefinition;
use Application\BLL\Document\FotoDocument;
use Application\BLL\Document\LogoDocument;
use Application\BLL\Document\PlanDocument;
use Application\BLL\Document\ReportDocument;
use Application\Route\Dispatcher;
use Intouch\Framework\Configuration\MenuConfig;
use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Controllers\ActionResult;
use Intouch\Framework\Controllers\ActionViewResult;
use Intouch\Framework\Controllers\CacheTableDataResult;
use Intouch\Framework\Controllers\ErrorResult;
use Intouch\Framework\Controllers\ErrorViewResult;
use Intouch\Framework\Controllers\FileDataResult;
use Intouch\Framework\Controllers\ViewResult;
use Intouch\Framework\Dao\ConnectionTypeEnum;
use Intouch\Framework\Dao\Queryable;
use Intouch\Framework\Document\GenericDocument;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Exceptions\BaseException;
use Intouch\Framework\MailHelper\Mail;
use Intouch\Framework\MailHelper\MailConfig;

 

$router = null;

$producto   =   Session::Instance()->producto;
$issetStatus    =   isset(Session::Instance()->producto);

// Asignar tipo de producto
if (!isset(Session::Instance()->producto)) {
    if (isset(Session::Instance()->usuario)) {
        Session::Instance()->producto = Session::Instance()->usuario->Cliente->TipoSistema;
    }
    else {
        Session::Instance()->producto = "RETAIL";
    }
}

$producto   =   Session::Instance()->producto;
 
// Asignar idioma
if (!isset(Session::Instance()->idioma)) {
    Session::Instance()->idioma = "es";
}

// Inicializar tipo de conexion por defecto
Queryable::Init(ConnectionTypeEnum::MYSQL);

// Inicializacion del generador de documentos
GenericDocument::AddToDictionary(DocumentTypeEnum::REPORTE, ReportDocument::class, ReportDocumentDefinition::class);
GenericDocument::AddToDictionary(DocumentTypeEnum::FOTO, FotoDocument::class, FotoDocumentDefinition::class);
GenericDocument::AddToDictionary(DocumentTypeEnum::AVATAR, AvatarDocument::class, AvatarDocumentDefinition::class);
GenericDocument::AddToDictionary(DocumentTypeEnum::LOGO, LogoDocument::class, LogoDocumentDefinition::class);
GenericDocument::AddToDictionary(DocumentTypeEnum::PLAN, PlanDocument::class, PlanDocumentDefinition::class);

// Obtener el ruteador
$router = new Dispatcher();
$GLOBALS['router'] = $router;

  

// if (isset(Session::Instance()->usuario)) {
//     $roles = Session::Instance()->usuario->Perfil->Roles;
// }
// else {
//     $roles = array();
// }


// Inicializar configuración de envío de correos
// Mail::$MailConfig = new MailConfig(
//    SmtpServer: SystemConfig::Instance()->SmtpServer,
//    SmtpUser: SystemConfig::Instance()->SmtpUser,
//    SmtpPassword: SystemConfig::Instance()->SmtpPassword,
//    SmtpPort: SystemConfig::Instance()->SmtpPort
// );

//$GLOBALS['menu_usuario'] = MenuConfig::FilterUserMenu($roles, $router);
$GLOBALS['menu_usuario'] = MenuConfig::FilterUserMenu($router, Session::Instance()->idioma, Session::Instance()->locale);

// Refrescar la estructura del menu widget
if (isset(Session::Instance()->usuario)) {
    $menu = MenuConfig::GetUserMenuWidget($GLOBALS['menu_usuario']);
    Session::Instance()->usuario->Funcionalidades = $GLOBALS['menu_usuario'];
    Session::Instance()->usuario->Menu = $menu;
}

//$auditoriaBO = new AuditoriaNavegacionBO();

// Despachar la solicitud
try {
    $result = $router->DispatchRequest(true);

    if ($result instanceof ErrorResult) {
        header('Content-Type: application/json');

        $json = json_encode($result);
        echo $json;
    }
    else if ($result instanceof ViewResult || $result instanceof ErrorViewResult) {
        header('Content-Type: text/html');

        echo $result->ViewContent;        
    }
    else if ($result instanceof ActionResult) {
        header('Content-Type: application/json');

        $json = json_encode($result);
        echo $json;
    }
    else if ($result instanceof CacheTableDataResult) {
        header('Content-Type: application/json');

        $json = json_encode($result->Result);
        echo $json;
    }
    else if ($result instanceof FileDataResult) {  
        echo $result->Result;
    }
    else if ($result instanceof ActionViewResult) {
        header('Content-Type: application/json');

        $json = json_encode($result, JSON_INVALID_UTF8_SUBSTITUTE);
        echo $json;
    }

    // if ($result->ErrorCode > 0) {

    // }

    //$auditoriaBO->GrabarAuditoria("OK");
}
catch (BaseException $ex) {
    //$auditoriaBO->GrabarAuditoria("Error", $ex);
}

// Log de auditoria de navegacion

// try {
//     $response = $dispatcher->dispatch($_SERVER['REQUEST_METHOD'], parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
//     // Print out the value returned from the dispatched function
    
//     if ($_SERVER['REDIRECT_STATUS']=="200") {
//         $auditoriaBO->GrabarAuditoria("OK");
//     }
//     else {
//         $auditoriaBO->GrabarAuditoria("Error");
//     }

//     echo $response;

// } catch (HttpRouteNotFoundException $e) {
//     $auditoriaBO->GrabarAuditoria("Route not found");
//     //echo "No se ha configurado la ruta <br>";
//     //echo "Error: Ruta no encontrada<br>";
// } catch (HttpMethodNotAllowedException $e) {
//     $auditoriaBO->GrabarAuditoria("Method not allowed: " + $_SERVER['REQUEST_METHOD']);
//     /*
//     echo "No se ha configurado la ruta <br>";
//     echo "Error: Ruta encontrada pero método no permitido<br>";
//      */
// } catch (\Exception $ex) {
//     http_response_code(404);

//     $auditoriaBO->GrabarAuditoria($ex->getMessage());

//     echo (new BaseController(''))->RenderShared('404', $ex);
//     die();
// }
