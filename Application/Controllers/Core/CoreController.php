<?php
namespace Application\Controllers\Core;

use Application\Resources\AssetManagerFactory;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\Configuration\ConnectionEnum;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Configuration\BundleConfig;
use Intouch\Framework\Configuration\ConnectionConfig;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\View\Display;
use Intouch\Framework\Mensajes\Mensaje;

#[Route(Path:'/core', Authorization: 'ALLOW_ALL')]
class CoreController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory(), forcedController:"Core");
    }

    #[Route(RequireSession:false)]
    #[ReturnViewResult]
    public function Login() 
    {
        // Obtener los idiomas
        $idiomas = Mensaje::ObtenerIdiomas();

        // Debemos obligar al singleton de display a actualizar los valores
        // que utiliza para la sesión
        Display::GetRenderer('', true);

        return $this->RenderView('login', $idiomas);
    }

    #[Route(RequireSession:false)]
    #[ReturnViewResult]
    public function Logout()
    {
        session_destroy();

        // Debemos obligar al singleton de display a actualizar los valores
        // que utiliza para la sesión
        Display::GetRenderer('', true);
        
        // Obtener los idiomas
        $idiomas = Mensaje::ObtenerIdiomas();

        return $this->RenderView('login', $idiomas);
    }

}