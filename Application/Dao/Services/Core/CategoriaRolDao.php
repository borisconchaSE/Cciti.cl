<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\CategoriaRol;
use Intouch\Framework\Dao\GenericDao;

class CategoriaRolDao extends GenericDao
{
    use CategoriaRolDaoT;

    function __construct($domain)
    {
        parent::__construct(
            CategoriaRol::class,
            $domain
        );
    }
}
