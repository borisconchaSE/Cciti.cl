<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWEntregadoExcelDao;
use Application\BLL\DataTransferObjects\Core\VWEntregadoExcelDto;
use Application\Dao\Entities\Core\VWEntregadoExcel;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWEntregadoExcelSvc extends GenericSvc
{
    use VWEntregadoExcelSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWEntregadoExcelDto::class, 
            VWEntregadoExcel::class,
            new VWEntregadoExcelDao($domain)
        );
    }

}