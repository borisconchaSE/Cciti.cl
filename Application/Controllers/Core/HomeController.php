<?php

namespace Application\Controllers\Core;

use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;

#[Route(Path:'/home')]
class HomeController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }
    
    #[Route(Path:'/', Authorization: 'ALLOW_ALL')]
    #[ReturnViewResult]
    public function Index()
    {
        return $this->RenderView('home');
    }

    #[Route(Authorization: 'ALLOW_ALL', RequireSession:false)]
    #[ReturnViewResult]
    public function Test()
    {
        return $this->RenderView('test');
    }

}