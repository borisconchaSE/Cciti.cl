<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Cliente;
use Intouch\Framework\Dao\GenericDao;

class ClienteDao extends GenericDao
{
    use ClienteDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Cliente::class,
            $domain
        );
    }
}
