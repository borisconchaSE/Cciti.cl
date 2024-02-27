<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoClave;
use Intouch\Framework\Dao\GenericDao;

class TipoClaveDao extends GenericDao
{
    use TipoClaveDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoClave::class,
            $domain
        );
    }
}
