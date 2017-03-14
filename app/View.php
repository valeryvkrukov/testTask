<?php 
namespace Test\App;

class View
{
	protected $rootDir = null;
	
	public function __construct()
	{
		$this->rootDir = realpath(__DIR__.'/../public/html');
	}
	
	public function render()
	{
		ob_start();
		require $this->rootDir . '/layout.html';
		echo ob_get_clean();
	}
	
	public function renderJSON($data)
	{
		header('Content-type: application/json');
		echo json_encode($data);
	}
}