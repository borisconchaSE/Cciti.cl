<?php
namespace Application\BLL\Services\Core;

use Application\Dao\Services\Core\ContactoDao;
use Application\BLL\DataTransferObjects\Core\ContactoDto;
use Application\Dao\Entities\Core\Contacto;
use Intouch\Framework\BLL\Service\GenericSvc;
use Intouch\Framework\Mapper\Mapper;

class ContactoSvc extends GenericSvc
{
    use ContactoSvcT;

    function __construct($domain)
    {
        parent::__construct(
            ContactoDto::class, 
            Contacto::class,
            new ContactoDao($domain)
        );
    }

}