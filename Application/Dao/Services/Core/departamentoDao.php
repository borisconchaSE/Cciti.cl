<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\departamento;
use Intouch\Framework\Dao\GenericDao;

class departamentoDao extends GenericDao
{
    use departamentoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            departamento::class,
            $domain
        );
    }
}
