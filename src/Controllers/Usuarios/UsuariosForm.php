<?php

namespace Controllers\Usuarios;

use Controllers\PublicController;
use Views\Renderer;
use Dao\Usuarios\Usuarios as UsuariosDao;
use Utilities\Site;
use Utilities\Validators;


class UsuariosForm extends PublicController{

    private $viewData = [];
    private $mode = "DSP";
    private $modeDescriptions = [
        "DSP" => "Detalle de Usuario",
        "INS" => "Nuevo Usuario",
        "UPD" => "Editar Usuario",
        "DEL" => "Eliminar Usuario"
    ];
    private $readonly = "";
    private $showCommitBtn = true;
    private $usuario =[
        "useremail" => "",
        "username" => "",
        "userpswd" => "",
        "userfching" => "",
        "userpswdest" => "",
        "userpswdexp" => "",
        "useractcod" => "",
        "userpswdchg" => "",
        "usertipo" => ""
    ];
    private $usuario_xss_token = "";

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
        Renderer::render("usuarios/form", $this->viewData);
        } catch (\Exception $ex) {
        Site::redirectToWithMsg(
            "index.php?page=Usuarios_UsuariosList",
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
                $this->usuario = UsuariosDao::getUsuarioById(intval($_GET["usercod"]));
                if (!$this->usuario) {
                    throw new \Exception("Usuario no encontrado.");
                }
            }
        } else {
            throw new \Exception("Modo no válido.");
        }
    }

    private function validateData(){

        if ($this->mode === "DEL"){
            $this -> usuario ["usercod"] = intval($_POST["usercod"] ?? "");
            return true;
        }

        $errors = [];
        $this->usuario_xss_token = $_POST["usuario_xss_token"] ?? "";
        $this->usuario["usercod"] = intval($_POST["usercod"] ?? "");
        $this->usuario["useremail"] = strval($_POST["useremail"] ?? "");
        $this->usuario["username"] = strval($_POST["username"] ?? "");
        $this->usuario["userpswd"] = strval($_POST["userpswd"] ?? "");
        $this->usuario["userfching"] = strval($_POST["userfching"] ?? "");
        $this->usuario["userpswdest"] = strval($_POST["userpswdest"] ?? "");
        $this->usuario["userpswdexp"] = strval($_POST["userpswdexp"] ?? "");
        $this->usuario["useractcod"] = strval($_POST["useractcod"] ?? "");
        $this->usuario["userpswdchg"] = strval($_POST["userpswdchg"] ?? "");
        $this->usuario["usertipo"] = strval($_POST["usertipo"] ?? "");

        if (Validators::isEmpty($this->usuario["useremail"])) {
            $errors[] = "El email del usuario es obligatorio.";
        } elseif (!Validators::IsValidEmail($this->usuario["useremail"])) {
            $errors[] = "El email del usuario no es válido.";
        }

        if (Validators::isEmpty($this->usuario["username"])) {
            $errors[] = "El nombre de usuario es obligatorio.";
        }

        if (Validators::isEmpty($this->usuario["userpswd"])) {
            $errors[] = "La contraseña del usuario es obligatoria.";
        } elseif (!Validators::IsValidPassword($this->usuario["userpswd"])) {
            $errors[] = "La contraseña del usuario no es válida.";
        }

        if (Validators::isEmpty($this->usuario["userfching"])) {
            $errors[] = "La fecha de creación del usuario es obligatoria.";
        }

        if (Validators::isEmpty($this->usuario["userpswdest"])) {
            $errors[] = "La fecha de expiración de la contraseña es obligatoria.";
        }

        if (Validators::isEmpty($this->usuario["userpswdexp"])) {
            $errors[] = "La fecha de expiración de la contraseña es obligatoria.";
        }

        if (Validators::isEmpty($this->usuario["useractcod"])) {
            $errors[] = "El código de activación del usuario es obligatorio.";
        }

        if (Validators::isEmpty($this->usuario["userpswdchg"])) {
            $errors[] = "La fecha de cambio de contraseña es obligatoria.";
        }

        if (count($errors) > 0) {
            foreach ($errors as $key => $value) {
                $this->usuario[$key] = $value;
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
        $result = UsuariosDao::insertUsuario(
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userpswd"],
            $this->usuario["userfching"],
            $this->usuario["userpswdest"],
            $this->usuario["userpswdexp"],
            $this->usuario["useractcod"],
            $this->usuario["userpswdchg"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_UsuariosList",
                "Usuario creado exitosamente."
            );
        } else {
            throw new \Exception("Error al crear el usuario.");
        }
    }

    private function handleUpdate()
    {
        $result = UsuariosDao::updateUsuario(
            $this->usuario["usercod"],
            $this->usuario["useremail"],
            $this->usuario["username"],
            $this->usuario["userpswd"],
            $this->usuario["userfching"],
            $this->usuario["userpswdest"],
            $this->usuario["userpswdexp"],
            $this->usuario["useractcod"],
            $this->usuario["userpswdchg"],
            $this->usuario["usertipo"]
        );
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_UsuariosList",
                "Usuario actualizado exitosamente."
            );
        } else {
            throw new \Exception("Error al actualizar el usuario.");
        }
    }

    private function handleDelete()
    {
        $result = UsuariosDao::deleteUsuario($this->usuario["usercod"]);
        if ($result > 0) {
            Site::redirectToWithMsg(
                "index.php?page=Usuarios_UsuariosList",
                "Usuario eliminado exitosamente."
            );
        } else {
            throw new \Exception("Error al eliminar el usuario.");
        }
    }

    private function setViewData()
    {
        $this->viewData["mode"] = $this->mode;
        $this->viewData["modeDescription"] = $this->modeDescriptions[$this->mode];
        $this->viewData["readonly"] = $this->readonly;
        $this->viewData["showCommitBtn"] = $this->showCommitBtn;
        $this->viewData["usuario"] = $this->usuario;
        $this->viewData["usuario_xss_token"] = $this->usuario_xss_token;
    }
}