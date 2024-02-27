<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\EntityField;
use Intouch\Framework\Annotation\Attributes\Entity;

class TipoIdioma
{
    use TipoIdiomaT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoIdioma = 0;
	public string $Codigo = '';
	public string $Descripcion = '';

    function __construct()
    {
    }
}