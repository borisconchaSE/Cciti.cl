<?php

namespace Application\Controllers\administracion;

use Application\BLL\BusinessObjects\Admin\AdministracionUsuariosBO;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\Attributes\ReturnViewResult;
use Intouch\Framework\Annotation\Attributes\Route;
use Intouch\Framework\Controllers\BaseController;

#[Route(Path:'/administracion/cuentas')]
class CuentasController extends BaseController {

    public function __construct() {
        parent::__construct(assetManagerFactory: new AssetManagerFactory());
    }
    
    #[Route(Path:'/', RequireSession:true,  Authorization: 'ENFORCED',Roles:['R_ADMIN_USERS'])]
    #[ReturnViewResult]
    public function Index()
    {

        ## OBTENEMOS LA LISTA DE USUARIOS CREADOS EN LA PLATAFORMA
        $ListaUsuarios  =   (new AdministracionUsuariosBO())->ObtenerUsuariosCreados();

        ## DEFINIMOS EL OBJETO QUE LE ENTREGAREMOS A LA VISTA PARA QUE TENGA ACCESO A LOS DATOS
        $data           =   [
            "ListaUsuarios" => $ListaUsuarios
        ];  

        ## LE PASAMOS LOS DATOS A LA VISTA, DIBUJAMOS Y RETORNAMOS LA VISTA YA PROCESADA PARA QUE EL 
        ## DISPACHER PUEDA ENTREGAR EL RESULTADO AL CLIENTE
        return $this->RenderView('home',$data);
    }

 

}