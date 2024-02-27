<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoVisualizacionDao;
use Application\BLL\DataTransferObjects\Core\TipoVisualizacionDto;
use Application\Dao\Entities\Core\TipoVisualizacion;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoVisualizacionSvc extends GenericSvc
{
    use TipoVisualizacionSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoVisualizacionDto::class, 
            TipoVisualizacion::class,
            new TipoVisualizacionDao($domain)
        );
    }

}