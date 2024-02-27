<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class TipoCliente
{
    use TipoClienteT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoCliente = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}