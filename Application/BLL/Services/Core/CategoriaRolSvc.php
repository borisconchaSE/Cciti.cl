<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\CategoriaRolDao;
use Application\BLL\DataTransferObjects\Core\CategoriaRolDto;
use Application\Dao\Entities\Core\CategoriaRol;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class CategoriaRolSvc extends GenericSvc
{
    use CategoriaRolSvcT;

    function __construct($domain)
    {
        parent::__construct(
            CategoriaRolDto::class, 
            CategoriaRol::class,
            new CategoriaRolDao($domain)
        );
    }

}