<?php
namespace App\Core;

class Router {

    private static array $routes = [];

    public static function get(string $uri, string $action) {
        self::$routes['GET'][$uri] = $action;
    }
	public static function post(string $uri, string $action) {
    self::$routes['POST'][$uri] = $action;
}


    public static function dispatch(): bool
{
    $method = $_SERVER['REQUEST_METHOD'];

    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $uri = str_replace(BASE_URL, '', $uri);
    $uri = trim($uri, '/');

    if (!isset(self::$routes[$method])) {
        return false;
    }

    foreach (self::$routes[$method] as $route => $action) {

        // Converte {id} em regex
        $pattern = preg_replace('#\{[^/]+\}#', '([^/]+)', $route);
        $pattern = '#^' . trim($pattern, '/') . '$#';

        if (preg_match($pattern, $uri, $matches)) {

            array_shift($matches); // remove match completo

            [$controller, $methodAction] = explode('@', $action);
            $controller = "App\\Controllers\\$controller";

            if (!class_exists($controller)) {
                throw new \Exception("Controller {$controller} não existe");
            }

            $instance = new $controller();

            if (!method_exists($instance, $methodAction)) {
                throw new \Exception("Método {$methodAction} não existe");
            }

            // 🔥 aqui está a mágica
            call_user_func_array([$instance, $methodAction], $matches);

            return true;
        }
    }

    return false;
}
}
