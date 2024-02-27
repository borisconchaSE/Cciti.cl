<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Rol;
use Intouch\Framework\Dao\GenericDao;

class RolDao extends GenericDao
{
    use RolDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Rol::class,
            $domain
        );
    }
}
