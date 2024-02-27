<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\stock;
use Intouch\Framework\Dao\GenericDao;

class stockDao extends GenericDao
{
    use stockDaoT;

    function __construct($domain)
    {
        parent::__construct(
            stock::class,
            $domain
        );
    }
}
