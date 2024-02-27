<?php
namespace Application\Dao\Services\Clientes;

use Application\Dao\Entities\Clientes\Clientes;
use Intouch\Framework\Dao\GenericDao;

class ClientesDao extends GenericDao
{
    use ClientesDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Clientes::class,
            $domain
        );
    }
}
