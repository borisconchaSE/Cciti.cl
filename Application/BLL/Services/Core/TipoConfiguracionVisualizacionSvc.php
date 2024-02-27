<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoConfiguracionVisualizacionDao;
use Application\BLL\DataTransferObjects\Core\TipoConfiguracionVisualizacionDto;
use Application\Dao\Entities\Core\TipoConfiguracionVisualizacion;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoConfiguracionVisualizacionSvc extends GenericSvc
{
    use TipoConfiguracionVisualizacionSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoConfiguracionVisualizacionDto::class, 
            TipoConfiguracionVisualizacion::class,
            new TipoConfiguracionVisualizacionDao($domain)
        );
    }

}