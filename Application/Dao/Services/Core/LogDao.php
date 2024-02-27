<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Log;
use Intouch\Framework\Dao\GenericDao;

class LogDao extends GenericDao
{
    use LogDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Log::class,
            $domain
        );
    }
}
