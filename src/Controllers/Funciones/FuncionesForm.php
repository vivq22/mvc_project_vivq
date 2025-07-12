<?php

namespace Controllers\Funciones;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Funciones\Funciones as DaoFunciones;
use Utilities\Site;
use Utilities\Validators;


class FuncionesForm extends PublicController{

    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Función",
        "INS" => "Nueva Función",
        "UPD" => "Editar Función",
        "DEL" => "Eliminar Función"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $funcion =[
        "fncod" => "",
        "fndsc" => "",
        "fnest" => "",
        "fntyp" => "",
    ];
    private $funcion_xss_token = "";

    public function run ():void 
    {
        try {
        $this->getData();
        if ($this->isPostBack()) {
            if ($this->validateData()) {
            $this->handlePostAction();
            }
        }
        $this->setViewData();
        Renderer::render("funciones/form", $this->viewData);
        } catch (\Exception $ex) {
        Site::redirectToWithMsg(
            "index.php?page=Funciones_FuncionesList",
            $ex->getMessage()
        );
        }
    }

    private function getData(){
        $this->mode = $_GET["mode"] ?? "NOF";
        if (isset($this->modeDescriptions[$this->mode])) {
            $this->readonly = $this->mode === "DEL" ? "readonly" : "";
            $this->showCommitBtn = $this->mode !== "DSP";
            if ($this->mode !== "INS") {
                $this->funcion = DaoFunciones::getFuncionById(intval($_GET["fncod"]));
                if (!$this->funcion) {
                    throw new \Exception("Función no encontrada.");
                }
            }
        } else {
            throw new \Exception("Modo no válido.");
        }
    }

    private function validateData(){

        if ($this->mode === "DEL"){
            $this -> funcion ["fncod"] = intval($_POST["fncod"] ?? "");
            return true;
        }

        $errors = [];
        $this->funcion_xss_token = $_POST["funcion_xss_token"] ?? "";
        $this->funcion["fncod"] = intval($_POST["fncod"] ?? "");
        $this->funcion["fndsc"] = strval($_POST["fndsc"] ?? "");
        $this->funcion["fnest"] = strval($_POST["fnest"] ?? "");
        $this->funcion["fntyp"] = strval($_POST["fntyp"] ?? "");

        if (Validators::isEmpty($this->funcion["fndsc"])) {
            $errors[] = "La descripción de la función es obligatoria.";
        }

        if (Validators::isEmpty($this->funcion["fnest"])) {
            $errors[] = "El estado de la función es obligatorio.";
        }
        if (Validators::isEmpty($this->funcion_xss_token)) {
            $errors[] = "Token de seguridad no válido.";
        } elseif (!Validators::IsValidXssToken($this->funcion_xss_token)) {
            $errors[] = "Token de seguridad no válido.";
        }
        if (Validators::isEmpty($this->funcion["fncod"]) && $this->mode !== "INS") {
            $errors[] = "Código de la función no válido.";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->funcion[$key] = $value;
            }
            return false;
        }
        return true;
    }

    private function handlePostAction()
    {
        switch ($this->mode) {
            case "INS":
                $this->handleInsert();
                break;
            case "UPD":
                $this->handleUpdate();
                break;
            case "DEL":
                $this->handleDelete();
                break;
            default:
                throw new \Exception("Modo no válido para la acción.", 1);
        }
    }

    private function handleInsert()
    {
        $result = FuncionesDao::insertFuncion(
            $this->funcion["fndsc"],
            $this->funcion["fnest"],
            $this->funcion["fntyp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_FuncionesList",
                "Función creada exitosamente."
            );
        } else {
            throw new \Exception("Error al crear la función.");
        }
    }

    private function handleUpdate()
    {
        $result = FuncionesDao::updateFuncion(
            $this->funcion["fncod"],
            $this->funcion["fndsc"],
            $this->funcion["fnest"],
            $this->funcion["fntyp"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_FuncionesList",
                "Función actualizada exitosamente."
            );
        } else {
            throw new \Exception("Error al actualizar la función.");
        }
    }

    private function handleDelete()
    {
        $result = FuncionesDao::deleteFuncion($this->funcion["fncod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Funciones_FuncionesList",
                "Función eliminada exitosamente."
            );
        } else {
            throw new \Exception("Error al eliminar la función.");
        }
    }


    private function setViewData()
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["modeDescription"] = $this->modeDescriptions[$this->mode];
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["funcion"] = $this->funcion;
        $this->viewData["funcion_xss_token"] = $this->funcion_xss_token;
    }
}