<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWGastosExcel;
use Intouch\Framework\Dao\GenericDao;

class VWGastosExcelDao extends GenericDao
{
    use VWGastosExcelDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWGastosExcel::class,
            $domain
        );
    }
}
