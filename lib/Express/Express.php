<?php

// see expressjs.com/guide.html
class Express {
	
	public static function createServer(Express_Logger_Interface $logger = null, Express_BodyParser_Interface $parser = null) {
		return new Express_Server($logger, $parser);
	}
	
}

class Express_Server {
	public $logger;
	public $parser;
	public $routes = array();
	public $lookup; // instead of $routes use a collection called Routes
	public $router;
	public static $defaultConfig = array(
		
	);
	public function __construct(Express_Logger_Interface $logger = null, Express_BodyParser_Interface $parser = null) {
		$this->logger = $logger;
		$this->parser = $parser;
		$this->config = self::$defaultConfig;
	}
	public function configure($name, $value) {
		$this->config['value'] = $value;
	}
	public function enable() {
		
	}
	public function enabled() {
		
	}
	public function disable() {
		
	}
	public function disabled() {
		
	}
	public function redirect() {
		
	}
	public function helpers() {
		
	}
	public function dynamicHelpers() {
		
	}
	public function match() {
		
	}
	public function mounted() {
		
	}
	public function register() {
		
	}
	public function useLogger(Express_Logger_Interface $logger) {
		$this->logger = $logger;
	}
	public function useParser(Express_BodyParser_Interface $parser) {
		$this->parser = $parser;
	}
	public function useRouter(Express_Router $router) {
		$this->router = $router;
	}
	public function get($route, $callback) {
		$this->routes[] = new Express_Route($this, 'get', $route, $callback);
	}
	public function post($route, $callback) {
		$this->routes[] = new Express_Route($this, 'post', $route, $callback);
	}
	public function put($route, $callback) {
		$this->routes[] = new Express_Route($this, 'put', $route, $callback);
	}
	public function delete($route, $callback) {
		$this->routes[] = new Express_Route($this, 'delete', $route, $callback);
	}
	public function all($route, $callback) {
		$this->routes[] = new Express_Route($this, 'all', $route, $callback);
	}
	public function set($name, $value) {
		// ?
	}
	public function error($callback) {
		// ?
	}
	public function listen() {
		if (!$this->logger) {
			$this->logger = new Express_Logger($this);
		}
		if (!$this->parser) {
			$this->parser = new Express_BodyParser($this);
		}
		if (!$this->router) {
			$this->router = new Express_Router($this);
		}
		$this->request = new Express_Request($this);
		$this->response = new Express_Response($this);
		// decide which route to use
		$callbacks = $this->router->getCallbacks();
		for ($i = 0; $i < count($callbacks); ++$i) {
			call_user_func($callbacks[$i], 
				$this->request, 
				$this->response, 
				new Express_Next($this, $callbacks[$i+1]) // a callable object: $next()
			);
		}
		
	}
}

class Express_Next {
	
	public function __construct(Express_Server_Interface $server, $callback) {
		$this->server = $server;
		$this->callback = $callback;
	}
	
	public function __invoke() {
		$args = func_get_args();
		// put args into $this->server->request or add onto callback using call_user_func_array
		call_user_func($callback,
			$this->server->request,
			$this->server->response,
			$this->callback ?: $this->server->get404Callback()
		);
	}
	
}

class Express_Router {
	
	public function __construct(Express_Server_Interface $server) {
		$this->server = $server;
	}
	
	public function getCallbacks() {
		$callbacks = array();
		foreach ($this->server->routes as $route) {
			if ($route->matches()) {
				$callbacks[] = $route->callback;
			}
		}
		return $callbacks;
	}
	
}

class Express_Route {
	
	public function __construct(Express_Server_Interface $server, $method, $route, $callback) {
		$this->server = $server;
		$this->method = $method;
		$this->route = $route;
		$this->callback = $callback;
	}
	
	public function matches() {
		
	}
	
}

class Express_Request {
	
	public $server;
	
	public $body; // raw request body
	
	public $session;
	
	public function __construct(Express_Server_Interface $server) {
		$this->server = $server;
		$this->session = new Express_Session($this);
		// TODO: populate get, post, etc.
	}
	
	public function header($name, $defaultValue) {
		// return the header with that name
	}
	
	public function accepts($type) {
		// check if browser accepts that type
	}
	
	public function is($type) {
		// TODO: use a regex and recognize special types like "an image"
		return $this->header('Content-Type') == $type;
	}
	
	public function flash($type, $message) {
		// queue a flash message
	}
	
	public function isXMLHttpRequest() {
		
	}
	
}

class Express_Response {
	
	public $output;
	
	public $charset;
	
	public function render($view, $options = array()) {
		
	}
	
	public function partial($view, $options = array()) {
		
	}
	
	public function send($str) {
		$this->output .= $str;
	}
	
	public function __destruct() {
		$this->sendHeaders();
		echo $this->output;
	}
	
	public function redirect($url, $status = null) {
		
	}
	
	public function contentType() {
		
	}
	
	public function attachment() {
		
	}
	
	public function sendfile() {
		
	}
	
	public function download() {
		
	}
	
	public function json() {
		
	}
	
	public function cookie($name, $value, $options = array()) {
		
	}
	
	public function clearCookie($name, $options = array()) {
		
	}
	
	public function local($name, $value = null) {
		// get or set the given variable name
	}
	
	public function locals($values) {
		
	}
	
}
