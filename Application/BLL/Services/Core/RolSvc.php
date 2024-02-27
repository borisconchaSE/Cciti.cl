<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\RolDao;
use Application\BLL\DataTransferObjects\Core\RolDto;
use Application\Dao\Entities\Core\Rol;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class RolSvc extends GenericSvc
{
    use RolSvcT;

    function __construct($domain)
    {
        parent::__construct(
            RolDto::class, 
            Rol::class,
            new RolDao($domain)
        );
    }

}