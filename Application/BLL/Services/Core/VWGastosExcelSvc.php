<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWGastosExcelDao;
use Application\BLL\DataTransferObjects\Core\VWGastosExcelDto;
use Application\Dao\Entities\Core\VWGastosExcel;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWGastosExcelSvc extends GenericSvc
{
    use VWGastosExcelSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWGastosExcelDto::class, 
            VWGastosExcel::class,
            new VWGastosExcelDao($domain)
        );
    }

}