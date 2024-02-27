<?php
namespace Application\Dao\Services\Core;

use Application\Dao\Entities\Core\Anno;
use Intouch\Framework\Dao\GenericDao;

class AnnoDao extends GenericDao
{
    use AnnoDaoT;

    function __construct($domain)
    {
        parent::__construct(
            Anno::class,
            $domain
        );
    }
}
