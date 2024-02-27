<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\stockDao;
use Application\BLL\DataTransferObjects\Core\stockDto;
use Application\Dao\Entities\Core\stock;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class stockSvc extends GenericSvc
{
    use stockSvcT;

    function __construct($domain)
    {
        parent::__construct(
            stockDto::class, 
            stock::class,
            new stockDao($domain)
        );
    }

}