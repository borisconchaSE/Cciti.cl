<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoVisualizacion;
use Intouch\Framework\Dao\GenericDao;

class TipoVisualizacionDao extends GenericDao
{
    use TipoVisualizacionDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoVisualizacion::class,
            $domain
        );
    }
}
