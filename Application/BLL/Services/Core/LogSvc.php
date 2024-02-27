<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\LogDao;
use Application\BLL\DataTransferObjects\Core\LogDto;
use Application\Dao\Entities\Core\Log;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class LogSvc extends GenericSvc
{
    use LogSvcT;

    function __construct($domain)
    {
        parent::__construct(
            LogDto::class, 
            Log::class,
            new LogDao($domain)
        );
    }

}