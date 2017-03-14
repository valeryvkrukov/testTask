<?php 
namespace Test\App;

use Test\App\View;

class Main
{
	protected static $__instance = null;
	
	protected function __construct()
	{
		$this->__initApp();
	}
	
	protected function __clone()
	{
	}
	
	public static function init()
	{
		if (self::$__instance === null) {
			self::$__instance = new self();
		}
		return self::$__instance;
	}
	
	protected function __initApp()
	{
		$basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
		$uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
		$uri = '/' . trim($uri, '/');
		$view = new View();
		if ($uri === '/') {
			$view->render();
		} else {
			$classname = explode('/', $uri);
			$classname = array_pop($classname);
			$adapter = 'Test\\App\\Adapter\\' . ucfirst($classname);
			if (class_exists($adapter)) {
				$data = stream_get_contents(fopen('php://input', 'r'));
				$data = json_decode($data, true);
				$adapter = new $adapter();
				$json = $adapter->loadResource($data);
				$view->renderJSON($json);
			}
		}
	}
	
}