<?php
namespace Application\Dao\Services\<<NAMESPACE>>;

use Application\Dao\Entities\<<NAMESPACE>>\<<ENTITY>>;
use Intouch\Framework\Dao\GenericDao;

class <<ENTITY>>Dao extends GenericDao
{
    use <<ENTITY>>DaoT;

    function __construct($domain)
    {
        parent::__construct(
            <<ENTITY>>::class,
            $domain
        );
    }
}
