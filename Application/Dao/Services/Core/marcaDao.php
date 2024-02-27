<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\marca;
use Intouch\Framework\Dao\GenericDao;

class marcaDao extends GenericDao
{
    use marcaDaoT;

    function __construct($domain)
    {
        parent::__construct(
            marca::class,
            $domain
        );
    }
}
