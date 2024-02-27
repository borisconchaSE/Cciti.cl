<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\ubicacion;
use Intouch\Framework\Dao\GenericDao;

class ubicacionDao extends GenericDao
{
    use ubicacionDaoT;

    function __construct($domain)
    {
        parent::__construct(
            ubicacion::class,
            $domain
        );
    }
}
