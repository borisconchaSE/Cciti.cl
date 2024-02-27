<?php
namespace Application\BLL\Services\Core;

trait RolPorPerfilSvcT
{
    public $innerMappings = [];
    
    public function GetByPerfil($idPerfil)
    {
        return $this->GetByForeign("IdPerfil", $idPerfil);
    }
}