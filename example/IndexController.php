<?php

namespace controller;

use src\core\MoonRouterHttp;

class IndexController
{
    public function index(MoonRouterHttp $req): void
    {
        var_dump($req);
        echo("call IndexController");
    }
}