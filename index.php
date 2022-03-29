<?php

session_start();

use App\Controllers\AddProductsController;
use App\Controllers\ListProductsController;
use App\Redirect;
use App\Repositories\Products\MySqlProductsRepository;
use App\Repositories\Products\ProductsRepository;
use App\View;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

use function DI\create;

require_once 'vendor/autoload.php';

$builder = new DI\ContainerBuilder();
$builder->addDefinitions([
    ProductsRepository::class => create(MySqlProductsRepository::class)
]);

$container = $builder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', [ListProductsController::class, 'list']);

    $r->addRoute('GET', '/add', [AddProductsController::class, 'add']);
    $r->addRoute('POST', '/', [AddProductsController::class, 'store']);
});

// Fetch method and URI from somewhere
$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

// Strip query string (?foo=bar) and decode URI
if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        // ... 404 Not Found
        var_dump('404 Not Found');
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        // ... 405 Method Not Allowed
        var_dump('405 Method Not Allowed');
        break;
    case FastRoute\Dispatcher::FOUND:
        $controller = $routeInfo[1][0];
        $method = $routeInfo[1][1];

        $vars = $routeInfo[2];
        /** @var View $response */
        $response = ($container->get($controller))->$method($vars);

        $loader = new FilesystemLoader('app/Views');
        $twig = new Environment($loader);

        if($response instanceof View) {
            echo $twig->render($response->getPath() . '.html', $response->getVariables());
        }

        if($response instanceof Redirect) {
            header('Location: ' . $response->getLocation());
            exit;
        }
        break;
}