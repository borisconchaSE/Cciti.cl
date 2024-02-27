<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\modeloDao;
use Application\BLL\DataTransferObjects\Core\modeloDto;
use Application\Dao\Entities\Core\modelo;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class modeloSvc extends GenericSvc
{
    use modeloSvcT;

    function __construct($domain)
    {
        parent::__construct(
            modeloDto::class, 
            modelo::class,
            new modeloDao($domain)
        );
    }

}