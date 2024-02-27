<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\TipoIdioma;
use Intouch\Framework\Dao\GenericDao;

class TipoIdiomaDao extends GenericDao
{
    use TipoIdiomaDaoT;

    function __construct($domain)
    {
        parent::__construct(
            TipoIdioma::class,
            $domain
        );
    }
}
