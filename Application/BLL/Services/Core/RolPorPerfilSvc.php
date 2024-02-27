<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\RolPorPerfilDao;
use Application\BLL\DataTransferObjects\Core\RolPorPerfilDto;
use Application\Dao\Entities\Core\RolPorPerfil;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class RolPorPerfilSvc extends GenericSvc
{
    use RolPorPerfilSvcT;

    function __construct($domain)
    {
        parent::__construct(
            RolPorPerfilDto::class, 
            RolPorPerfil::class,
            new RolPorPerfilDao($domain)
        );
    }

}