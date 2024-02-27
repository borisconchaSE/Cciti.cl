<?php
namespace Application\Dao\Entities\Core;

use Intouch\Framework\Annotation\Attributes\Entity;
use Intouch\Framework\Annotation\Attributes\EntityField;

class ConfiguracionVisualizacion
{
    use ConfiguracionVisualizacionT;
    
	#[EntityField(PrimaryKey: true)]
	public int $IdConfiguracionVisualizacion = 0;
	public int $IdEmpresa = 0;
	public int $IdTipoConfiguracionVisualizacion = 0;

    function __construct()
    {
    }
}