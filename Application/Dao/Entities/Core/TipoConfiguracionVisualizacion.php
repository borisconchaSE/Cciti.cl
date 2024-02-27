<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

class TipoConfiguracionVisualizacion
{
    use TipoConfiguracionVisualizacionT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdTipoConfiguracionVisualizacion = 0;
	public int $IdTipoVisualizacion = 0;
	public string $Descripcion = '';
	public string $Vigente = '';

    function __construct()
    {
    }
}