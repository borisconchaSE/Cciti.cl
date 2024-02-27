<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\empresa;
use Intouch\Framework\Dao\GenericDao;

class empresaDao extends GenericDao
{
    use empresaDaoT;

    function __construct($domain)
    {
        parent::__construct(
            empresa::class,
            $domain
        );
    }
}
