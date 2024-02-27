<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\precioUnitarioDao;
use Application\BLL\DataTransferObjects\Core\precioUnitarioDto;
use Application\Dao\Entities\Core\precioUnitario;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class precioUnitarioSvc extends GenericSvc
{
    use precioUnitarioSvcT;

    function __construct($domain)
    {
        parent::__construct(
            precioUnitarioDto::class, 
            precioUnitario::class,
            new precioUnitarioDao($domain)
        );
    }

}