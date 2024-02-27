<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Empresa;
use Intouch\Framework\Dao\GenericDao;

class EmpresaDao extends GenericDao
{
    use EmpresaDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Empresa::class,
            $domain
        );
    }
}
