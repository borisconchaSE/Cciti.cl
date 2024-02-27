<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Usuario;
use Intouch\Framework\Dao\GenericDao;

class UsuarioDao extends GenericDao
{
    use UsuarioDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Usuario::class,
            $domain
        );
    }
}
