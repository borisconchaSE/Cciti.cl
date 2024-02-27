<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\estadoOC;
use Intouch\Framework\Dao\GenericDao;

class estadoOCDao extends GenericDao
{
    use estadoOCDaoT;

    function __construct($domain)
    {
        parent::__construct(
            estadoOC::class,
            $domain
        );
    }
}
