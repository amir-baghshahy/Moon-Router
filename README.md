# Moon-Router
this is simple php Router Support Controller

### Features
- Supports GET, POST, PUT, DELETE ANY request methods.
- Controllers support (Example: [indexcontroller,"index"]).
- Namespaces supports
- fast an easy to use 
- Route param supports
- No dependency




## Example Usage
```php
use Moon\Route\Router;

require_once("./Router.php");

$router = new Router();

$router->get('/', function () {
    return "moon";
});

$router->get("/moon/{id}", function ($param) {
    echo ($param['id']);
});

$router->get("/moon", [indexController::class, "index"]);

$router->run();
```


## Docs
##### to use need php < 7  
##### $router = new Router();
##### $router->method(callable function or array of controller and method)
##### at end must run router 
##### $router->run();
##### and run your project white this command  (php -S localhost:8000 index.php) 


## Licence
[MIT Licence][mit-url]
