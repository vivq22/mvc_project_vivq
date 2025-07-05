<?php

namespace Controllers\Examen;

use Controllers\PublicController;
use Dao\Examen\Productos as ProductosDAO;
use Views\Renderer;

class Productos extends PublicController
{
    private array $viewData;

    public function __construct()
    {
        $this->viewData = [
            "productos" => []
        ];
    }

    public function run(): void
    {
        $this->viewData["productos"] = ProductosDAO::getProductos();
        Renderer::render("examen/productos", $this->viewData);
    }
}
