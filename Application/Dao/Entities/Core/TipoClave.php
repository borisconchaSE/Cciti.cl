<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class TipoClave
{
    use TipoClaveT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoClave = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}