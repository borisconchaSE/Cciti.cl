<?php
namespace Application\BLL\DataTransferObjects\Core;

class CategoriaRolDto
{
    use CategoriaRolDtoT;

    public function __construct(
		public int $IdCategoriaRol = 0,
		public string $Decripcion = ''
    ) {

    }
}