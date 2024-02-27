<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\centrocostos;
use Intouch\Framework\Dao\GenericDao;

class centrocostosDao extends GenericDao
{
    use centrocostosDaoT;

    function __construct($domain)
    {
        parent::__construct(
            centrocostos::class,
            $domain
        );
    }
}
