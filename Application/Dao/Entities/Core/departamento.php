<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

#[Entity(Schema: '')]
class departamento
{
    use departamentoT;
    
	#[EntityField(PrimaryKey: true)]
	public int $idDepto = 0;
	public string $Descripcion = '';
	public ?int $IdEmpresa = null;
	public ?int $idubicacion = null;

    function __construct()
    {
    }
}