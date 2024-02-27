<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\estadoFC;
use Intouch\Framework\Dao\GenericDao;

class estadoFCDao extends GenericDao
{
    use estadoFCDaoT;

    function __construct($domain)
    {
        parent::__construct(
            estadoFC::class,
            $domain
        );
    }
}
