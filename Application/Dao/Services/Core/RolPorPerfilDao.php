<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\RolPorPerfil;
use Intouch\Framework\Dao\GenericDao;

class RolPorPerfilDao extends GenericDao
{
    use RolPorPerfilDaoT;

    function __construct($domain)
    {
        parent::__construct(
            RolPorPerfil::class,
            $domain
        );
    }
}
