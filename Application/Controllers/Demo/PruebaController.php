<?php

namespace Application\Controllers\Demo;

use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;

#[Route(Path:'/demo/prueba')]
class PruebaController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }
    
    #[Route(Authorization: 'ALLOW_ALL',RequireSession:true, Roles:['R_ADMIN_USERS'])]
    #[ReturnViewResult]
    public function Index()
    {
        return $this->RenderView('home');
    } 

}