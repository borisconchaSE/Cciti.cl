<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWCompraExcelDao;
use Application\BLL\DataTransferObjects\Core\VWCompraExcelDto;
use Application\Dao\Entities\Core\VWCompraExcel;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWCompraExcelSvc extends GenericSvc
{
    use VWCompraExcelSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWCompraExcelDto::class, 
            VWCompraExcel::class,
            new VWCompraExcelDao($domain)
        );
    }

}