<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWStockExcelDao;
use Application\BLL\DataTransferObjects\Core\VWStockExcelDto;
use Application\Dao\Entities\Core\VWStockExcel;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWStockExcelSvc extends GenericSvc
{
    use VWStockExcelSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWStockExcelDto::class, 
            VWStockExcel::class,
            new VWStockExcelDao($domain)
        );
    }

}