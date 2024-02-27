<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\estadoOCDao;
use Application\BLL\DataTransferObjects\Core\estadoOCDto;
use Application\Dao\Entities\Core\estadoOC;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class estadoOCSvc extends GenericSvc
{
    use estadoOCSvcT;

    function __construct($domain)
    {
        parent::__construct(
            estadoOCDto::class, 
            estadoOC::class,
            new estadoOCDao($domain)
        );
    }

}