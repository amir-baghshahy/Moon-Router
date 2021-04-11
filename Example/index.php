<?php

use Moon\Route\Router;

require_once("../Router.php");

$router = new Router();

$router->get('/', function () {
    return "moon";
});

$router->get("/moon/{id}", function ($param) {
    echo ($param['id']);
});

$router->get("/moon", [indexController::class, "index"]);

$router->run();