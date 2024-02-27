<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoIdiomaDto
{
    use TipoIdiomaDtoT;

    public function __construct(
		public int $IdTipoIdioma = 0,
		public string $Codigo = '',
		public string $Descripcion = ''
    ) {

    }
}