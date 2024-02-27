<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class empresa
{
    use empresaT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdEmpresa = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}