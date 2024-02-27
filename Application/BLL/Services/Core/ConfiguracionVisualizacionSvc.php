<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\ConfiguracionVisualizacionDao;
use Application\BLL\DataTransferObjects\Core\ConfiguracionVisualizacionDto;
use Application\Dao\Entities\Core\ConfiguracionVisualizacion;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ConfiguracionVisualizacionSvc extends GenericSvc
{
    use ConfiguracionVisualizacionSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ConfiguracionVisualizacionDto::class, 
            ConfiguracionVisualizacion::class,
            new ConfiguracionVisualizacionDao($domain)
        );
    }

}