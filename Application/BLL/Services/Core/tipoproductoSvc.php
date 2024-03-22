<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\tipoproductoDao;
use Application\BLL\DataTransferObjects\Core\tipoproductoDto;
use Application\Dao\Entities\Core\tipoproducto;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class tipoproductoSvc extends GenericSvc
{
    use tipoproductoSvcT;

    function __construct($domain)
    {
        parent::__construct(
            tipoproductoDto::class, 
            tipoproducto::class,
            new tipoproductoDao($domain)
        );
    }

}