<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Contacto;
use Intouch\Framework\Dao\GenericDao;

class ContactoDao extends GenericDao
{
    use ContactoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Contacto::class,
            $domain
        );
    }
}
