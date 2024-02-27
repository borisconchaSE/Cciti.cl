<?php
namespace Application\BLL\Services\Core;

use Application\BLL\DataTransferObjects\Core\ClienteDto;
use Application\BLL\DataTransferObjects\Core\ContactoDto;
use Application\BLL\DataTransferObjects\Core\EmpresaDto;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Intouch\Framework\Mapper\Mapper;

trait UsuarioSvcT
{
    public $innerMappings = [
        'Cliente'           => ClienteDto::class,
        'Contacto'          => ContactoDto::class,
        'Empresa'           => EmpresaDto::class,
        'Perfil'            => PerfilDto::class,
    ];

    public function Login($nombreUsuario, $clave)
    {
        return Mapper::ToDto( $this->Dao->Login($nombreUsuario, md5($clave)), $this->DtoName, $this->innerMappings);
    }

    public function BuscarUsuarioWithClienteByIdUsuarioAndCliente($IdUsuario,$IdCliente){
        return Mapper::ToDto( $this->Dao->BuscarUsuarioWithClienteByIdUsuarioAndCliente($IdUsuario, $IdCliente), $this->DtoName, $this->innerMappings);
    }

    public function TraerUsuariosParaGestor($idCliente) {
        return Mapper::ToDtos( $this->Dao->TraerUsuariosParaGestor($idCliente), $this->DtoName, $this->innerMappings);
    }
}