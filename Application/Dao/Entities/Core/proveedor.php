<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class proveedor
{
    use proveedorT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idProveedor = 0;
	public string $Nombre = '';
	public ?string $Rut = null;

    function __construct()
    {
    }
}