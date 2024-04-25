<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWActivosExcelDao;
use Application\BLL\DataTransferObjects\Core\VWActivosExcelDto;
use Application\Dao\Entities\Core\VWActivosExcel;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWActivosExcelSvc extends GenericSvc
{
    use VWActivosExcelSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWActivosExcelDto::class, 
            VWActivosExcel::class,
            new VWActivosExcelDao($domain)
        );
    }

}