<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWEntregadoExcel;
use Intouch\Framework\Dao\GenericDao;

class VWEntregadoExcelDao extends GenericDao
{
    use VWEntregadoExcelDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWEntregadoExcel::class,
            $domain
        );
    }
}
