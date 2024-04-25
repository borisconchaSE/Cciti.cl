<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWStockExcel;
use Intouch\Framework\Dao\GenericDao;

class VWStockExcelDao extends GenericDao
{
    use VWStockExcelDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWStockExcel::class,
            $domain
        );
    }
}
