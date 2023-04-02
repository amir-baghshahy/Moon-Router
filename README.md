# Moon-Router

this is simple php Router Support Controller

### Features
- Support GET,POST,PUT,DELETE,PATCH,OPTIONS,ANY request methods.
- Support Redirect Routes (redirect,permanentRedirect).
- Support Route params (index/{name}).
- Support Controllers with namespace (Example: [indexcontroller,"index"]).
- Has a dedicated request class (Example: MoonRouterHttp::param("key")).
- Fast and easy to use.
- No dependency

## Example Usage

```php

use src\MoonRouter;
use src\core\MoonRouterHttp;

require __DIR__ . '/../vendor/autoload.php';

MoonRouter::get('/', function () {
    echo("index");
});

MoonRouter::get("/moon/{id}", function (MoonRouterHttp $req) {
    echo($req::param('id'));
});

MoonRouter::get("/moon", [\controller\IndexController::class, "index"]);


MoonRouter::post("/create",function (MoonRouterHttp $req){
    echo($req::post("name"));
});

MoonRouter::redirect("/test", "/");


-------------- MoonRouterHttp class --------------------------

MoonRouterHttp::post("key"):int|string // $_POST
MoonRouterHttp::server("key"):int|string// $_SERVER
MoonRouterHttp::file("key"):int|string // $_File
MoonRouterHttp::get("key"):int|string // $_GET
MoonRouterHttp::param("key"):int|string // route params
MoonRouterHttp::getBody(): array // return array of parameter $_POST or $_GET
MoonRouterHttp::getHttpMethod():string 
MoonRouterHttp::sendHttpCode(int|null $code = null): int
MoonRouterHttp::isPost():bool
MoonRouterHttp::isGet():bool
MoonRouterHttp::getUrlFromRequest():string 

```

## Docs

##### to use need php <= 8

##### and run your project white this command  (php -S localhost:8000 index.php)

## Licence

[MIT Licence][mit-url]
