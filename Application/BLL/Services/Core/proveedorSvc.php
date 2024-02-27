<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\proveedorDao;
use Application\BLL\DataTransferObjects\Core\proveedorDto;
use Application\Dao\Entities\Core\proveedor;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class proveedorSvc extends GenericSvc
{
    use proveedorSvcT;

    function __construct($domain)
    {
        parent::__construct(
            proveedorDto::class, 
            proveedor::class,
            new proveedorDao($domain)
        );
    }

}