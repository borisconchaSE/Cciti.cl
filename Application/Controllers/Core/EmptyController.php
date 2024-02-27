<?php

namespace Application\Controllers\Core;

use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Environment\Session;

#[Route(Path:'/', Authorization: 'ALLOW_ALL', RequireSession: false)]
class EmptyController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }
    
    #[Route(Path:"/",RequireSession: false)]
    #[ReturnViewResult]
    public function Index()
    {
        if (isset(Session::Instance()->usuario) && isset(Session::Instance()->usuario->Perfil)) {
            header('Location: ' . Session::Instance()->usuario->Perfil->LandingPage);
            die();
        }
        else {
            header('Location: /core/login');
            die();
        }
    }

}