<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\estadosactivos;
use Intouch\Framework\Dao\GenericDao;

class estadosactivosDao extends GenericDao
{
    use estadosactivosDaoT;

    function __construct($domain)
    {
        parent::__construct(
            estadosactivos::class,
            $domain
        );
    }
}
