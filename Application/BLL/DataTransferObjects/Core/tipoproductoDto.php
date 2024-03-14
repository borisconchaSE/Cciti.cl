<?php
namespace Application\BLL\DataTransferObjects\Core;

class tipoproductoDto
{
    use tipoproductoDtoT;

    public function __construct(
		public int $idTipoProducto = 0,
		public string $DescripcionProduto = ''
    ) {

    }
}