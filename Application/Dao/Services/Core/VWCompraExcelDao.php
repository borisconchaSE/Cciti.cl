<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWCompraExcel;
use Intouch\Framework\Dao\GenericDao;

class VWCompraExcelDao extends GenericDao
{
    use VWCompraExcelDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWCompraExcel::class,
            $domain
        );
    }
}
