<?php
namespace Application\BLL\Services\<<NAMESPACE>>;

use Application\Dao\Services\<<NAMESPACE>>\<<ENTITY>>Dao;
use Application\BLL\DataTransferObjects\<<NAMESPACE>>\<<ENTITY>>Dto;
use Application\Dao\Entities\<<NAMESPACE>>\<<ENTITY>>;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class <<ENTITY>>Svc extends GenericSvc
{
    use <<ENTITY>>SvcT;

    function __construct($domain)
    {
        parent::__construct(
            <<ENTITY>>Dto::class, 
            <<ENTITY>>::class,
            new <<ENTITY>>Dao($domain)
        );
    }

}