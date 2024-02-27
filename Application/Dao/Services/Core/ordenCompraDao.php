<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\ordenCompra;
use Intouch\Framework\Dao\GenericDao;

class ordenCompraDao extends GenericDao
{
    use ordenCompraDaoT;

    function __construct($domain)
    {
        parent::__construct(
            ordenCompra::class,
            $domain
        );
    }
}
