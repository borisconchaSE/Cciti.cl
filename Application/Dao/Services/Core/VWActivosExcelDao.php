<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWActivosExcel;
use Intouch\Framework\Dao\GenericDao;

class VWActivosExcelDao extends GenericDao
{
    use VWActivosExcelDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWActivosExcel::class,
            $domain
        );
    }
}
