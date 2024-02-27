<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\modelo;
use Intouch\Framework\Dao\GenericDao;

class modeloDao extends GenericDao
{
    use modeloDaoT;

    function __construct($domain)
    {
        parent::__construct(
            modelo::class,
            $domain
        );
    }
}
