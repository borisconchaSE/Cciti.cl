<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

class TipoVisualizacion
{
    use TipoVisualizacionT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoVisualizacion = 0;
	public string $Descripcion = '';

    function __construct()
    {
    }
}