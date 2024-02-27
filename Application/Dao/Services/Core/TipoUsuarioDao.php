<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoUsuario;
use Intouch\Framework\Dao\GenericDao;

class TipoUsuarioDao extends GenericDao
{
    use TipoUsuarioDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoUsuario::class,
            $domain
        );
    }
}
