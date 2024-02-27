<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\PerfilDao;
use Application\BLL\DataTransferObjects\Core\PerfilDto;
use Application\Dao\Entities\Core\Perfil;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class PerfilSvc extends GenericSvc
{
    use PerfilSvcT;

    function __construct($domain)
    {
        parent::__construct(
            PerfilDto::class, 
            Perfil::class,
            new PerfilDao($domain)
        );
    }

}