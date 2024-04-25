<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\VWExcelEntregado;
use Intouch\Framework\Dao\GenericDao;

class VWExcelEntregadoDao extends GenericDao
{
    use VWExcelEntregadoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            VWExcelEntregado::class,
            $domain
        );
    }
}
