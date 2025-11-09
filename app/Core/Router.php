<?php
namespace App\Core;

class Router {
    private array $routes = [];

    public function get(string $path, $callback): void {
        $this->routes['GET'][$path] = $callback;
    }
    
    public function post(string $path, $callback): void {
        $this->routes['POST'][$path] = $callback;
    }

    public function dispatch(string $uri, string $method): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $callback = $this->routes[$method][$path] ?? null;

        if (!$callback) {
            http_response_code(404);
            echo "<h1>404 Not Found</h1>";
            return;
        }

        // Handle controller@method strings
        if (is_string($callback) && strpos($callback, '@') !== false) {
            [$controllerName, $methodName] = explode('@', $callback);
            $controllerClass = "App\\Controllers\\$controllerName";
            
            if (!class_exists($controllerClass)) {
                http_response_code(500);
                echo "Controller $controllerClass not found.";
                return;
            }

            $controller = new $controllerClass();

            if (!method_exists($controller, $methodName)) {
                http_response_code(500);
                echo "Method $methodName not found in controller $controllerClass.";
                return;
            }

            echo $controller->$methodName();
            return;
        }

        // If it's an anonymous function, just call it
        echo call_user_func($callback);
    }

    public function redirect(string $path): void {
        header("Location: $path");
        exit;
    }
}
