<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoConfiguracionVisualizacion;
use Intouch\Framework\Dao\GenericDao;

class TipoConfiguracionVisualizacionDao extends GenericDao
{
    use TipoConfiguracionVisualizacionDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoConfiguracionVisualizacion::class,
            $domain
        );
    }
}
