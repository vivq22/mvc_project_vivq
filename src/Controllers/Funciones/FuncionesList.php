<?php

namespace Controllers\Funciones;

use Controllers\PublicController;
use Utilities\Context;
use Dao\Funciones\Funciones as DaoFunciones;
use Views\Renderer;

class FuncionesList extends PublicController{

    private $viewData = [];
    private $funciones = [];


    public function run():void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $this->funciones = DaoFunciones::getFunciones();
        $this->viewData["funciones"] = $this->funciones;
        Renderer::render("funciones/list", $this->viewData);
    }

    private function getParams(): void
    {
        // Aquí podrías obtener parámetros específicos de la solicitud, si es necesario.
        // Por ejemplo, filtros o paginación.
    }

    private function getParamsFromContext(): void
    {
        // Aquí podrías obtener parámetros del contexto, si es necesario.
        // Por ejemplo, información de sesión o del usuario.
    }
}