<?php
namespace Application\BLL\DataTransferObjects\Core;

class ContactoDto
{
    use ContactoDtoT;

    public function __construct(
		public int $IdContacto = 0,
		public string $Nombre = '',
		public string $Email = '',
		public string $Cargo = '',
		public ?string $Avatar = null
    ) {

    }
}