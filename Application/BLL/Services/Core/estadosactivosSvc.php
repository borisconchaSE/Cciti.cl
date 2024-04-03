<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\estadosactivosDao;
use Application\BLL\DataTransferObjects\Core\estadosactivosDto;
use Application\Dao\Entities\Core\estadosactivos;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class estadosactivosSvc extends GenericSvc
{
    use estadosactivosSvcT;

    function __construct($domain)
    {
        parent::__construct(
            estadosactivosDto::class, 
            estadosactivos::class,
            new estadosactivosDao($domain)
        );
    }

}