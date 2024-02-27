<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoProducto;
use Intouch\Framework\Dao\GenericDao;

class TipoProductoDao extends GenericDao
{
    use TipoProductoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoProducto::class,
            $domain
        );
    }
}
