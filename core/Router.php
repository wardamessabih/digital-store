<?php

namespace Core;

class Router {
    protected $routes = [];
    protected $groupPrefix = '';
    protected $groupMiddleware = [];

    public function get($path, $handler, $middleware = []) {
        $this->addRoute('GET', $path, $handler, $middleware);
    }

    public function post($path, $handler, $middleware = []) {
        $this->addRoute('POST', $path, $handler, $middleware);
    }

    public function put($path, $handler, $middleware = []) {
        $this->addRoute('PUT', $path, $handler, $middleware);
    }

    public function delete($path, $handler, $middleware = []) {
        $this->addRoute('DELETE', $path, $handler, $middleware);
    }

    public function getRoutes() {
        return $this->routes;
    }

    public function group($prefix, $callback, $middleware = []) {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix = $previousPrefix . $prefix;
        $this->groupMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    protected function addRoute($method, $path, $handler, $middleware = []) {
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $path);
        $pattern = '#^' . $this->groupPrefix . $pattern . '$#';

        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'pattern' => $pattern,
            'handler' => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware)
        ];
    }

    public function dispatch($uri, $method) {
        $uri = parse_url($uri, PHP_URL_PATH) ?? '/';
        
        $uriWithoutSlash = rtrim($uri, '/') ?: '/';
        $uriWithSlash = rtrim($uri, '/') . '/' ?: '/';
        
        $matched = false;
        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (preg_match($route['pattern'], $uriWithoutSlash, $matches) || preg_match($route['pattern'], $uriWithSlash, $matches)) {
                $matched = true;
                foreach ($route['middleware'] as $middleware) {
                    if (is_callable($middleware)) {
                        $result = $middleware();
                        if ($result === false) {
                            return;
                        }
                    }
                }

                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                if (is_array($route['handler'])) {
                    [$controller, $action] = $route['handler'];
                    if (class_exists($controller)) {
                        $instance = new $controller();
                        if (method_exists($instance, $action)) {
                            return call_user_func_array([$instance, $action], $params);
                        }
                    }
                } elseif (is_callable($route['handler'])) {
                    return call_user_func_array($route['handler'], $params);
                } elseif (is_string($route['handler']) && strpos($route['handler'], '@') !== false) {
                    [$controller, $action] = explode('@', $route['handler']);
                    $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';

                    if (file_exists($controllerFile)) {
                        require_once $controllerFile;
                        if (class_exists($controller)) {
                            $instance = new $controller();
                            if (method_exists($instance, $action)) {
                                return call_user_func_array([$instance, $action], $params);
                            }
                        }
                    }
                }
                break;
            }
        }

        if (!$matched) {
            $this->notFound();
        }
    }

    protected function notFound() {
        http_response_code(404);
        if (function_exists('Helper::isAjax')) {
            $isAjax = false;
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
                strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                $isAjax = true;
            }
            if ($isAjax) {
                echo json_encode(['error' => 'الصفحة غير موجودة']);
                exit;
            }
        }
        include __DIR__ . '/../views/errors/404.php';
        exit;
    }
}

function route($path, $params = []) {
    Config::load();
    $baseUrl = rtrim(Config::get('app.url', 'http://localhost'), '/');
    $path = ltrim($path, '/');

    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $path = preg_replace("/\{$key\}/", $value, $path);
        }
    }

    return $baseUrl . '/' . $path;
}

function asset($path) {
    Config::load();
    $baseUrl = rtrim(Config::get('app.url', 'http://localhost'), '/');
    return $baseUrl . '/assets/' . ltrim($path, '/');
}

function url($path = '') {
    Config::load();
    $baseUrl = rtrim(Config::get('app.url', 'http://localhost'), '/');
    return $baseUrl . '/' . ltrim($path, '/');
}
