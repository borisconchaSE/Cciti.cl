<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\VWExcelEntregadoDao;
use Application\BLL\DataTransferObjects\Core\VWExcelEntregadoDto;
use Application\Dao\Entities\Core\VWExcelEntregado;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class VWExcelEntregadoSvc extends GenericSvc
{
    use VWExcelEntregadoSvcT;

    function __construct($domain)
    {
        parent::__construct(
            VWExcelEntregadoDto::class, 
            VWExcelEntregado::class,
            new VWExcelEntregadoDao($domain)
        );
    }

}