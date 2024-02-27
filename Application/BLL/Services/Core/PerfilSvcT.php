<?php
namespace Application\BLL\Services\Core;

use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Intouch\Framework\Mapper\Mapper;

trait PerfilSvcT
{
    public $innerMappings = [];
    public static function HasRol(PerfilDto $perfil, $rol)
    {
        if ($rol == '')
            return true;
            
        foreach ($perfil->Roles as $key => $rolActual) {
            if ($rolActual->Codigo == $rol) {
                return true;
            }
        }

        return false;
    }

    public function BuscarPerfilConListaRoles(array $ListaRoles){

        return Mapper::ToDtos($this->Dao->BuscarPerfilConListaRoles($ListaRoles), $this->DtoName, $this->innerMappings);

    }

    public static function HasAnyRol(PerfilDto $perfil, $roles)
    {
        if (count($roles) == 0)
            return true;

        foreach ($roles as $key => $rol) {
            if (self::HasRol($perfil, $rol))
                return true;
        }

        return false;
    }

    private static function ObtenerUri($uriBuscadaConParametros)
    {
        if (isset($GLOBALS['funcionalidades'])) {
            // buscar el inicio del string (para el caso en que lleven parametros)
            $uriBuscada = parse_url($uriBuscadaConParametros, PHP_URL_PATH);
            
            foreach ($GLOBALS['funcionalidades'] as $uri => $funcionalidad) {
                if (strlen($uri) >= strlen($uriBuscada)) {
                    if (substr($uri, 0, strlen($uriBuscada)) == $uriBuscada) {
                        return $GLOBALS['funcionalidades'][$uri];
                    }
                }
            }
        }

        return null;
    }

    public function GetListPerfils() {
        return Mapper::ToDtos($this->Dao->GetListPerfils(), $this->DtoName);
    }
}