<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoClaveDto
{
    use TipoClaveDtoT;

    public function __construct(
		public int $IdTipoClave = 0,
		public string $Descripcion = ''
    ) {

    }
}