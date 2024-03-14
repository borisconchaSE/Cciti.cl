<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\ordencompra;
use Intouch\Framework\Dao\GenericDao;

class ordencompraDao extends GenericDao
{
    use ordencompraDaoT;

    function __construct($domain)
    {
        parent::__construct(
            ordencompra::class,
            $domain
        );
    }
}
