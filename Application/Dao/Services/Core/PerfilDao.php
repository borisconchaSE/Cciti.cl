<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Perfil;
use Intouch\Framework\Dao\GenericDao;

class PerfilDao extends GenericDao
{
    use PerfilDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Perfil::class,
            $domain
        );
    }
}
