<?php

namespace Controllers\Examen\Maintenance\Restaurants;

use Controllers\PublicController;
use Dao\Restaurants\Restaurants as RestaurantsDAO;
use Views\Renderer;

class Restaurants extends PublicController
{
    private array $viewData;
    public function __construct()
    {
        $this->viewData = [];
    }
    public function run(): void
    {
        $this->viewData["restaurants"] = RestaurantsDAO::getRestaurants();
        Renderer::render("maintenance/restaurants/restaurants", $this->viewData);
    }
}