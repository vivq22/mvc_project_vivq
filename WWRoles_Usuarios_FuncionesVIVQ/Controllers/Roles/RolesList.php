<?php

namespace Controllers\Roles;

use Controllers\PublicController;
use Utilities\Context;
use Utilities\Paging;
use Dao\Roles\Roles as DaoRoles;
use Views\Renderer;

class RolesList extends PublicController
{
    private $partialDescription = "";
    private $status = "";
    private $orderBy = "";
    private $orderDescending = false;
    private $pageNumber = 1;
    private $itemsPerPage = 10;
    private $viewData = [];
    private $roles = [];
    private $rolesCount = 0;
    private $pages = 0;

    public function run(): void
    {
        $this->getParamsFromContext();
        $this->getParams();
        $tmpRoles = DaoRoles::getRoles(
            $this->partialDescription,
            $this->status,
            $this->orderBy,
            $this->orderDescending,
            $this->pageNumber - 1,
            $this->itemsPerPage
        );
        $this->roles = $tmpRoles["roles"];
        $this->rolesCount = $tmpRoles["total"];
        $this->pages = $this->rolesCount > 0 ? ceil($this->rolesCount / $this->itemsPerPage) : 1;
        if ($this->pageNumber > $this->pages) {
            $this->pageNumber = $this->pages;
        }
        $this->setParamsToContext();
        $this->setParamsToDataView();
        Renderer::render("roles/list", $this->viewData);
    }

    private function getParams(): void
    {
        $this->partialDescription = $_GET["partialDescription"] ?? $this->partialDescription;
        $this->status = isset($_GET["status"]) && in_array($_GET["status"], ['ACT', 'INA', 'EMP']) ? $_GET["status"] : $this->status;
        if ($this->status === "EMP") {
            $this->status = "";
        }
        $this->orderBy = isset($_GET["orderBy"]) && in_array($_GET["orderBy"], ["rolescod", "rolesdsc", "clear"]) ? $_GET["orderBy"] : $this->orderBy;
        if ($this->orderBy === "clear") {
            $this->orderBy = "";
        }
        $this->orderDescending = isset($_GET["orderDescending"]) ? boolval($_GET["orderDescending"]) : $this->orderDescending;
        $this->pageNumber = isset($_GET["pageNum"]) ? intval($_GET["pageNum"]) : $this->pageNumber;
        $this->itemsPerPage = isset($_GET["itemsPerPage"]) ? intval($_GET["itemsPerPage"]) : $this->itemsPerPage;
    }

    private function getParamsFromContext(): void
    {
        $this->partialDescription = Context::getContextByKey("roles_partialDescription");
        $this->status = Context::getContextByKey("roles_status");
        $this->orderBy = Context::getContextByKey("roles_orderBy");
        $this->orderDescending = boolval(Context::getContextByKey("roles_orderDescending"));
        $this->pageNumber = intval(Context::getContextByKey("roles_page"));
        $this->itemsPerPage = intval(Context::getContextByKey("roles_itemsPerPage"));
        if ($this->pageNumber < 1) $this->pageNumber = 1;
        if ($this->itemsPerPage < 1) $this->itemsPerPage = 10;
    }

    private function setParamsToContext(): void
    {
        Context::setContext("roles_partialDescription", $this->partialDescription, true);
        Context::setContext("roles_status", $this->status, true);
        Context::setContext("roles_orderBy", $this->orderBy, true);
        Context::setContext("roles_orderDescending", $this->orderDescending, true);
        Context::setContext("roles_page", $this->pageNumber, true);
        Context::setContext("roles_itemsPerPage", $this->itemsPerPage, true);
    }

    private function setParamsToDataView(): void
    {
        $this->viewData["partialDescription"] = $this->partialDescription;
        $this->viewData["status"] = $this->status;
        $this->viewData["orderBy"] = $this->orderBy;
        $this->viewData["orderDescending"] = $this->orderDescending;
        $this->viewData["pageNum"] = $this->pageNumber;
        $this->viewData["itemsPerPage"] = $this->itemsPerPage;
        $this->viewData["rolesCount"] = $this->rolesCount;
        $this->viewData["pages"] = $this->pages;
        $this->viewData["roles"] = $this->roles;

        if ($this->orderBy !== "") {
            $orderByKey = "Order" . ucfirst($this->orderBy);
            $orderByKeyNoOrder = "OrderBy" . ucfirst($this->orderBy);
            $this->viewData[$orderByKeyNoOrder] = true;
            if ($this->orderDescending) {
                $orderByKey .= "Desc";
            }
            $this->viewData[$orderByKey] = true;
        }

        $statusKey = "status_" . ($this->status === "" ? "EMP" : $this->status);
        $this->viewData[$statusKey] = "selected";

        $pagination = Paging::getPagination(
            $this->rolesCount,
            $this->itemsPerPage,
            $this->pageNumber,
            "index.php?page=Roles_RolesList",
            "Roles_RolesList"
        );
        $this->viewData["pagination"] = $pagination;
    }
}
?>