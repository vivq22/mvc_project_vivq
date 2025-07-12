<?php

namespace Controllers\Roles;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Roles\Roles as DaoRoles;
use Utilities\Site;
use Utilities\Validators;


class RolesForm extends PublicController{

    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Rol",
        "INS" => "Nuevo Rol",
        "UPD" => "Editar Rol",
        "DEL" => "Eliminar Rol"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $rol =[
        "rolesest" => "",
        "rolesdsc" => "",
    ];
    private $rol_xss_token = "";

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
        Renderer::render("roles/form", $this->viewData);
        } catch (\Exception $ex) {
        Site::redirectToWithMsg(
            "index.php?page=Roles_RolesList",
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
                $this->rol = DaoRoles::getRolById(intval($_GET["rolescod"]));
                if (!$this->rol) {
                    throw new \Exception("Rol no encontrado.");
                }
            }
        } else {
            throw new \Exception("Modo no válido.");
        }
    }

    private function validateData(){

        if ($this->mode === "DEL"){
            $this -> rol ["rolescod"] = intval($_POST["rolescod"] ?? "");
            return true;
        }

        $errors = [];
        $this->rol_xss_token = $_POST["rol_xss_token"] ?? "";
        $this->rol["rolescod"] = intval($_POST["rolescod"] ?? "");
        $this->rol["rolesdsc"] = strval($_POST["rolesdsc"] ?? "");
        $this->rol["rolesest"] = strval($_POST["rolesest"] ?? "");

        if (Validators::isEmpty($this->rol["rolesdsc"])) {
            $errors[] = "La descripción del rol es obligatoria.";
        }

        if (Validators::isEmpty($this->rol["rolesest"])) {
            $errors[] = "El estado del rol es obligatorio.";
        }
        if (Validators::isEmpty($this->rol_xss_token)) {
            $errors[] = "Token de seguridad no válido.";
        } elseif (!Validators::IsValidXssToken($this->rol_xss_token)) {
            $errors[] = "Token de seguridad no válido.";
        }
        if (Validators::isEmpty($this->rol["rolescod"]) && $this->mode !== "INS") {
            $errors[] = "Código del rol no válido.";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->rol[$key] = $value;
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
        $result = RolesDao::insertRol(
            $this->rol["rolesdsc"],
            $this->rol["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol creado exitosamente."
            );
        } else {
            throw new \Exception("Error al crear el rol.");
        }
    }

    private function handleUpdate()
    {
        $result = RolesDao::updateRol(
            $this->rol["rolescod"],
            $this->rol["rolesdsc"],
            $this->rol["rolesest"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol actualizado exitosamente."
            );
        } else {
            throw new \Exception("Error al actualizar el rol.");
        }
    }

    private function handleDelete()
    {
        $result = RolesDao::deleteRol($this->rol["rolescod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Roles_RolesList",
                "Rol eliminado exitosamente."
            );
        } else {
            throw new \Exception("Error al eliminar el rol.");
        }
    }


    private function setViewData()
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["modeDescription"] = $this->modeDescriptions[$this->mode];
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["rol"] = $this->rol;
        $this->viewData["rol_xss_token"] = $this->rol_xss_token;
    }
}