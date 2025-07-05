<?php

namespace Controllers\Examen\Maintenance\Restaurants;

use Controllers\PublicController;
use Dao\Restaurants\Restaurants as RestaurantsDAO;
use Views\Renderer;

use Utilities\Site;
use Utilities\Validators;

const LIST_URL = "index.php?page=Examen-Maintenance-Restaurants-Restaurants";

class Restaurant extends PublicController
{
    private array $viewData;
    private array $modes;
    public function __construct()
    {
        $this->viewData = [
            "mode" => "",
            "id_restaurante" => 0,
            "nombre" => "",
            "tipo_cocina" => "",
            "ubicacion" => "",
            "calificacion" => 0,
            "capacidad_comensales" => 0,
            "modeDsc" => "",
            "errors" => [],
            "cancelLabel" => "Cancel",
            "showConfirm" => true,
            "readonly" => ""
        ];
        $this->modes = [
            "INS" => "New Restaurant",
            "UPD" => "Updating %s",
            "DEL" => "Deleting %s",
            "DSP" => "Details of %s"
        ];
    }
    public function run(): void
    {

        /*
        1 - Cargar la data de query params
        2 - Determinamos el MODO de ejecución del Formulario
        3 - Si el MODO no es INS
            3.1 - Cargar los datos de la DB
        4 - Si la solicitud Postback  (POST | PUT | DELETE)
            4.1 - Cargar los datos del Body ( POST )
            4.2 - Validar los datos
            4.3 - Ejecutar el métodos según el MODO
            4.4 - Mostrar mensaje y enviar a Listados
        5 - Prepara la data para la vista
        6 - Renderizar la Vista
        */
        $this->getQueryParamsData();
        if ($this->viewData["mode"] !== "INS") {
            $this->getDataFromDB();
        }
        if ($this->isPostBack()) {
            $this->getBodyData();
            if ($this->validateData()) {
                $this->processData();
            }
        }
        $this->prepareViewData();
        Renderer::render("maintenance/restaurants/restaurant", $this->viewData);
    }

    private function throwError(string $message, string $logMessage = "")
    {
        if (!empty($logMessage)) {
            error_log(sprintf("%s - %s", $this->name, $this->$logMessage));
        }
        Site::redirectToWithMsg(LIST_URL, $message);
    }
    private function innerError(string $scope, string $message)
    {
        if (!isset($this->viewData["errors"][$scope])) {
            $this->viewData["errors"][$scope] = [$message];
        } else {
            $this->viewData["errors"][$scope][] = $message;
        }
    }

    private function getQueryParamsData()
    {
        if (!isset($_GET["mode"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Attempt to load controler without the required query parameters MODE"
            );
        }
        $this->viewData["mode"] = $_GET["mode"];
        if (!isset($this->modes[$this->viewData["mode"]])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Attempt to load controler with  wrong value on query parameter MODE - " . $this->viewData["mode"]
            );
        }
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["id_restaurante"])) {
                $this->throwError(
                    "Something went wrong, try again.",
                    "Attempt to load controler without the required query parameters ID"
                );
            }
            if (!is_numeric($_GET["id_restaurante"])) {
                $this->throwError(
                    "Something went wrong, try again.",
                    "Attempt to load controler with  wrong value on query parameter ID - " . $_GET["id_restaurante"]
                );
            }
            $this->viewData["id_restaurante"] = intval($_GET["id_restaurante"]);
        }
    }

    private function getDataFromDB()
    {
        $tmpRestaurant = RestaurantsDAO::getRestaurantsById(
            $this->viewData["id_restaurante"]
        );
        if ($tmpRestaurant && count($tmpRestaurant) > 0) {
            $this->viewData["nombre"] = $tmpRestaurant["nombre"];
            $this->viewData["tipo_cocina"] = $tmpRestaurant["tipo_cocina"];
            $this->viewData["ubicacion"] = $tmpRestaurant["ubicacion"];
            $this->viewData["calificacion"] = $tmpRestaurant["calificacion"];
            $this->viewData["capacidad_comensales"] = $tmpRestaurant["capacidad_comensales"];
        } else {
            $this->throwError(
                "Something went wrong, try again.",
                "Record for id " . $this->viewData["id_restaurante"] . " not found."
            );
        }
    }

    private function getBodyData()
    {
        if (!isset($_POST["id_restaurante"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter ID on body"
            );
        }
        if (!isset($_POST["nombre"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter NOMBRE on body"
            );
        }

        if (!isset($_POST["tipo_cocina"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter TIPO_COCINA on body"
            );
        }

        if (!isset($_POST["ubicacion"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter UBICACION on body"
            );
        }

        if (!isset($_POST["calificacion"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter CALIFICACION on body"
            );
        }

        if (!isset($_POST["capacidad_comensales"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter CAPACIDAD_COMENSALES on body"
            );
        }

        if (!isset($_POST["xsrtoken"])) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post without parameter XSRTOKEN on body"
            );
        }
        if (intval($_POST["id_restaurante"]) !== $this->viewData["id_restaurante"]) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post with inconsistent parameter ID value has: " . $this->viewData["id_restaurante"] . " recieved: " . $_POST["id_restaurante"]
            );
        }
        if ($_POST["xsrtoken"] !==  $_SESSION[$this->name . "-xsrtoken"]) {
            $this->throwError(
                "Something went wrong, try again.",
                "Trying to post with inconsistent parameter XSRToken value has: " . $_SESSION[$this->name . "-xsrtoken"] . " recieved: " . $_POST["xsrtoken"]
            );
        }

        $this->viewData["id_restaurante"] = intval($_POST["id_restaurante"]);
        $this->viewData["nombre"] = $_POST["nombre"];
        $this->viewData["tipo_cocina"] = $_POST["tipo_cocina"];
        $this->viewData["ubicacion"] = $_POST["ubicacion"];
        $this->viewData["calificacion"] = floatval($_POST["calificacion"]);
        $this->viewData["capacidad_comensales"] = intval($_POST["capacidad_comensales"]);
    }

    private function validateData(): bool
    {
        if (Validators::IsEmpty($this->viewData["nombre"])) {
            $this->innerError("nombre", "This field is required.");
        }
        if (strlen($this->viewData["tipo_cocina"]) > 255) {
            $this->innerError("tipo_cocina", "Value is too long. Maximun allowed 255 character.");
        }
        if (strlen($this->viewData["ubicacion"]) > 255) {
            $this->innerError("ubicacion", "Value is too long. Maximun allowed 255 character.");
        }
        if (!in_array($this->viewData["calificacion"], $this->status)) {
            $this->innerError("calificacion", "This field is required.");
        }

        if ($this->viewData["capacidad_comensales"] <= 0) {
            $this->innerError("capacidad_comensales", "This field is required and must be greater than 0.");
        }

        return !(count($this->viewData["errors"]) > 0);
    }

    private function processData()
    {
        $mode = $this->viewData["mode"];
        switch ($mode) {
            case "INS":
                if (RestaurantsDAO::newRestaurant(
                    $this->viewData["nombre"],
                    $this->viewData["tipo_cocina"],
                    $this->viewData["ubicacion"],
                    $this->viewData["calificacion"],
                    $this->viewData["capacidad_comensales"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Restaurant created successfuly");
                } else {
                    $this->innerError("global", "Something wrong happend to save the new Category.");
                }
                break;
            case "UPD":
                if (RestaurantsDAO::updateRestaurant(
                    $this->viewData["id_restaurante"],
                    $this->viewData["nombre"],
                    $this->viewData["tipo_cocina"],
                    $this->viewData["ubicacion"],
                    $this->viewData["calificacion"],
                    $this->viewData["capacidad_comensales"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Restaurant updated successfuly");
                } else {
                    $this->innerError("global", "Something wrong happend while updating the restaurant.");
                }
                break;
            case "DEL":
                if (RestaurantsDAO::deleteRestaurant(
                    $this->viewData["id_restaurante"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Restaurant deleted successfuly");
                } else {
                    $this->innerError("global", "Something wrong happend while deleting the category.");
                }
                break;
        }
    }
    private function prepareViewData()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["nombre"]
        );
        $this->viewData['selected' . $this->viewData["tipo_cocina"]] = "selected";
        $this->viewData['selected' . $this->viewData["ubicacion"]] = "selected";
        $this->viewData['selected' . $this->viewData["calificacion"]] = "selected";
        $this->viewData['selected' . $this->viewData["capacidad_comensales"]] = "selected";
        $this->viewData["errors"] = [];
        if (!isset($this->viewData["errors"]["global"])) {
            $this->viewData["errors"]["global"] = [];
        }
        $this->viewData["errors"]["global"] = array_merge(
            $this->viewData["errors"]["global"],
            [
                "mode" => $this->viewData["mode"],
                "id_restaurante" => $this->viewData["id_restaurante"]
            ]
        );
        if (count($this->viewData["errors"]) > 0) {
            foreach ($this->viewData["errors"] as $scope => $errorsArray) {
                $this->viewData["errors_" . $scope] = $errorsArray;
            }
        }

        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["cancelLabel"] = "Back";
            $this->viewData["showConfirm"] = false;
        }

        if ($this->viewData["mode"] === "DSP" || $this->viewData["mode"] === "DEL") {
            $this->viewData["readonly"] = "readonly";
        }
        $this->viewData["timestamp"] = time();
        $this->viewData["xsrtoken"] = hash("sha256", json_encode($this->viewData));
        $_SESSION[$this->name . "-xsrtoken"] = $this->viewData["xsrtoken"];
    }
}