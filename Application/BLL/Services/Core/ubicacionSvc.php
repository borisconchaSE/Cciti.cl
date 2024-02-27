<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\ubicacionDao;
use Application\BLL\DataTransferObjects\Core\ubicacionDto;
use Application\Dao\Entities\Core\ubicacion;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ubicacionSvc extends GenericSvc
{
    use ubicacionSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ubicacionDto::class, 
            ubicacion::class,
            new ubicacionDao($domain)
        );
    }

}