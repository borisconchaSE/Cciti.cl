<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\precioUnitario;
use Intouch\Framework\Dao\GenericDao;

class precioUnitarioDao extends GenericDao
{
    use precioUnitarioDaoT;

    function __construct($domain)
    {
        parent::__construct(
            precioUnitario::class,
            $domain
        );
    }
}
