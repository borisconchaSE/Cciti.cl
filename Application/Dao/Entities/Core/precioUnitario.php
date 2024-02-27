<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class precioUnitario
{
    use precioUnitarioT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idunitario = 0;
	public string $Descripcion = '';
	public int $Valor_Actual = 0;
	public int $Valor_Anterior = 0;

    function __construct()
    {
    }
}