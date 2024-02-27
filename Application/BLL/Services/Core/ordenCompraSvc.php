<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\ordenCompraDao;
use Application\BLL\DataTransferObjects\Core\ordenCompraDto;
use Application\Dao\Entities\Core\ordenCompra;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ordenCompraSvc extends GenericSvc
{
    use ordenCompraSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ordenCompraDto::class, 
            ordenCompra::class,
            new ordenCompraDao($domain)
        );
    }

}