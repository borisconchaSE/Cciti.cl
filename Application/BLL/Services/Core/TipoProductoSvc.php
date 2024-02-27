<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\TipoProductoDao;
use Application\BLL\DataTransferObjects\Core\TipoProductoDto;
use Application\Dao\Entities\Core\TipoProducto;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class TipoProductoSvc extends GenericSvc
{
    use TipoProductoSvcT;

    function __construct($domain)
    {
        parent::__construct(
            TipoProductoDto::class, 
            TipoProducto::class,
            new TipoProductoDao($domain)
        );
    }

}