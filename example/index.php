<?php


use src\core\MoonRouterHttp;
use src\MoonRouter;

require __DIR__ . '/../vendor/autoload.php';


MoonRouter::get('/', function () {
    echo("index");
});

MoonRouter::get("/moon/{id}", function (MoonRouterHttp $req) {
    echo($req::param('id'));
});


MoonRouter::get("/moon", [\controller\IndexController::class, "index"]);


MoonRouter::post("/moon/{id}", function (MoonRouterHttp $req) {
    echo($req::param('id'));
});

MoonRouter::redirect("/test", "/");

MoonRouter::permanentRedirect("/test2", "/");
