<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoCliente;
use Intouch\Framework\Dao\GenericDao;

class TipoClienteDao extends GenericDao
{
    use TipoClienteDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoCliente::class,
            $domain
        );
    }
}
