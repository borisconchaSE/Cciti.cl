<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\tipoproducto;
use Intouch\Framework\Dao\GenericDao;

class tipoproductoDao extends GenericDao
{
    use tipoproductoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            tipoproducto::class,
            $domain
        );
    }
}
