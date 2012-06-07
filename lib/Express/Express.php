<?php

// see expressjs.com/guide.html
//class Express {
//	
//	public static function createServer(Express_Logger_Interface $logger = null, Express_BodyParser_Interface $parser = null) {
//		return new Express_Server($logger, $parser);
//	}
//	
//}

interface Express_Server_Interface {
	
}

class Express_Forward extends Exception {
	
}

class Express_Server implements Express_Server_Interface {
	public $logger;
	public $parser;
	public $routes = array();
	public $lookup; // instead of $routes use a collection of Routes called $lookup
	public $router;
	public $request;
	public $respones;
	public $_forwardLimit = 16;
	public static $defaultConfig = array(
		
	);
//	public function __construct(Express_Logger_Interface $logger = null, Express_BodyParser_Interface $parser = null) {
//		$this->logger = $logger;
//		$this->parser = $parser;
//		$this->config = self::$defaultConfig;
//	}
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
	public function forward($toUrl) {
		throw new Express_Forward($toUrl);
	}
	public function redirect($toUrl) {
		
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
//	public function useLogger(Express_Logger_Interface $logger) {
//		$this->logger = $logger;
//	}
//	public function useParser(Express_BodyParser_Interface $parser) {
//		$this->parser = $parser;
//	}
//	public function useRouter(Express_Router_Interface $router) {
//		$this->router = $router;
//	}
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
	public function get404Callback() {
		return function($req, $res) {
			$res->send('<h1>404 Page Not Found</h1>');
		};
	}
	public function listen() {
//		if (!$this->logger) {
//			$this->logger = new Express_Logger($this);
//		}
//		if (!$this->parser) {
//			$this->parser = new Express_BodyParser($this);
//		}
		if (!$this->router) {
			$this->router = new Express_Router($this);
		}
		$this->request = new Express_Request($this);
		$this->response = new Express_Response($this);
		// decide which route to use
		$callbacks = $this->router->getCallbacks();
		try {
			$next = new Express_Next($this, $callbacks);
			$result = $next();
			echo $this->response->output;
			return $result;
		}
		catch (Express_Forward $fwd) {
			if (--$this->_forwardLimit == 0) {
				echo '<h1>500 Server Error</h1>';
				die;
			}
			$toUrl = $fwd->getMessage();
			$_GET['express_php_url'] = ltrim($toUrl, '/');
			$this->listen();
		}
	}
}

class Express_Next {
	
	public function __construct(Express_Server_Interface $server, $callbacks) {
		$this->server = $server;
		$this->callbacks = $callbacks;
	}
	
	public function __invoke() {
		$args = func_get_args();
		// TODO: put args into $this->server->request or add onto callback using call_user_func_array
		// checkout what ExpressJS does
		if (count($this->callbacks) > 0) {
			$callback = $this->callbacks[0];
			$next = new Express_Next($this->server, array_slice($this->callbacks, 1));
		}
		else {
			$callback = $this->server->get404Callback();
			$next = function() {};
		}
		call_user_func(
			$callback,
			$this->server->request,
			$this->server->response,
			$next
		);
	}
	
}

class FunctionCurry {
	
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
		$params = array();
		$route = trim($this->route, '/');
		//$regex = preg_quote($route, '@');
		$regex = $route;
		$idx = 0;
		$regex = preg_replace_callback('@(\:[\w_]+|\*)@', function($match) use (&$params, &$idx) {
			if ($match[1] == '*') {
				$params[$idx] = $idx;
			}
			else {
				$params[$idx] = trim($match[1], ':');
			}
			$idx++;
			return '([^/]+)';
		}, $regex);
		$regex = "@^$regex@";
		preg_match($regex, $_GET['express_php_url'], $match);
		if (preg_match($regex, $_GET['express_php_url'], $match)) {
			foreach ($params as $idx => $param) {
				$this->server->request->params[$param] = $match[$idx+1];
			}
			return true;
		}
		return false;
	}
	
}

class Express_Request {
	
	public $server;
	
	public $body; // raw request body
	
	public $params = array();
	
	public function __construct(Express_Server_Interface $server) {
		$this->server = $server;
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
	
	public $server;
	
	public $output = '';
	
	public $charset = 'utf-8';
	
	public function __construct(Express_Server_Interface $server) {
		$this->server = $server;
		// TODO: populate get, post, etc.
	}	
	
	public function render($view, $options = array()) {
		
	}
	
	public function partial($view, $options = array()) {
		
	}
	
	public function send($str) {
		$this->output .= $str;
	}
	
	public function forward($toUrl) {
		return $this->server->forward($toUrl);
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
