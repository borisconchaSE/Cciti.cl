<?php
namespace Application\BLL\DataTransferObjects\Core;

class TipoVisualizacionDto
{
    use TipoVisualizacionDtoT;

    public function __construct(
		public int $IdTipoVisualizacion = 0,
		public string $Descripcion = ''
    ) {

    }
}