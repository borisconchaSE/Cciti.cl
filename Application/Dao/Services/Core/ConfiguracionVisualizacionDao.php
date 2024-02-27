<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\ConfiguracionVisualizacion;
use Intouch\Framework\Dao\GenericDao;

class ConfiguracionVisualizacionDao extends GenericDao
{
    use ConfiguracionVisualizacionDaoT;

    function __construct($domain)
    {
        parent::__construct(
            ConfiguracionVisualizacion::class,
            $domain
        );
    }
}
