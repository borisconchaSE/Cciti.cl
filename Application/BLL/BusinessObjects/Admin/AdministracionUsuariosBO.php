<?php

namespace Application\BLL\BusinessObjects\Admin;
 
use Application\BLL\DataTransferObjects\Core\UsuarioDto;
use Application\BLL\Services\Clientes\ClientesSvc;
use Application\BLL\Services\Core\UsuarioSvc;
use Application\Configuration\ConnectionEnum;
use Application\Dao\Entities\Clientes\Clientes;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Dao\BindVariable;

class AdministracionUsuariosBO 
{

    public function ObtenerUsuariosCreados() 
    {
        
        ## EL PRIMER PASO, ES OBTENER LA LISTA DE USUARIOS QUE SE ENCUENTRAN GENERADOS EN LA BBDD

        ## PARA ESO INICIAMOS INSTANCIANDO EL SERVICE CORRESPONDIENTE
                            ## al generar el service debemos especificar la conexión a usar (solo nombre)
   

        $UsuarioSvc     =   new UsuarioSvc(ConnectionEnum::CORE); 



        ## en segundo lugar, obtenemos todos los registros disponibles en la BBDD
        ## para esto usamos la propiedad GetAll del ORM interno del framework
        $ListaUsuarios  =   $UsuarioSvc->GetAll();

        ## por ultimo, retornamos la lista de usuarios
        return $ListaUsuarios;

    }

    public function GetRoles (UsuarioDto|null $usuario) {

        $roles = [];

        if (!isset($usuario) || $usuario == null) {
            return [];
        }

        return $roles;
    }
}