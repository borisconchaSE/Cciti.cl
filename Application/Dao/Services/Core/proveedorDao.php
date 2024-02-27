<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\proveedor;
use Intouch\Framework\Dao\GenericDao;

class proveedorDao extends GenericDao
{
    use proveedorDaoT;

    function __construct($domain)
    {
        parent::__construct(
            proveedor::class,
            $domain
        );
    }
}
