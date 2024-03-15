<?php

namespace Application\Controllers\Core;

use Application\BLL\BusinessObjects\Core\CompraBO;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;

#[Route(Path:'/generales')]
class GeneralesController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }
    
    #[Route(Path:'/', Authorization: 'ALLOW_ALL')]
    #[ReturnViewResult]
    public function Index()
    {
        ## OBTENEMOS LA LISTA DE COMPRAS DE LA BD
        $ListaCompras  =   (new CompraBO())->CargarTablaGeneralCompras();

        ## CREAMOS EL OBJETO EL CUAL ENTREGARA LOS DATOS A LA VISTA
        $data           =   [
            "ListaCompras" => $ListaCompras
        ];
        
        ## LE PASAMOS LOS DATOS A LA VISTA, DIBUJAMOS Y RETORNAMOS LA VISTA YA PROCESADA PARA QUE EL 
        ## DISPACHER PUEDA ENTREGAR EL RESULTADO AL CLIENTE
        return $this->RenderView('home',$data);
    }

    #[Route(Authorization: 'ALLOW_ALL', RequireSession:false)]
    #[ReturnViewResult]
    public function Test()
    {
        return $this->RenderView('test');
    }
 

}