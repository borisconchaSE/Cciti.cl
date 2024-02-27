<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\estadoFCDao;
use Application\BLL\DataTransferObjects\Core\estadoFCDto;
use Application\Dao\Entities\Core\estadoFC;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class estadoFCSvc extends GenericSvc
{
    use estadoFCSvcT;

    function __construct($domain)
    {
        parent::__construct(
            estadoFCDto::class, 
            estadoFC::class,
            new estadoFCDao($domain)
        );
    }

}