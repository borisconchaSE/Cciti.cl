<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\departamentoDao;
use Application\BLL\DataTransferObjects\Core\departamentoDto;
use Application\Dao\Entities\Core\departamento;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class departamentoSvc extends GenericSvc
{
    use departamentoSvcT;

    function __construct($domain)
    {
        parent::__construct(
            departamentoDto::class, 
            departamento::class,
            new departamentoDao($domain)
        );
    }

}