<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

class Cliente
{
    use ClienteT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdCliente = 0;
	public int $IdTipoCliente = 0;
	public string $RazonSocial = '';
	public string $Rut = '';
	public string $Direccion = '';
	public int $Eliminado = 0;
	public string $UriLogo = '';
	public int $IdTipoProducto = 0;

    function __construct()
    {
    }
}