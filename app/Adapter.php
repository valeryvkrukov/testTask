<?php 
namespace Test\App;

abstract class Adapter
{
	protected $config;
	protected $mapping = [];
	protected $adapterName;
	protected $adapterOptions;
	
	public function __construct()
	{
		$this->loadConfig();
	}

	protected function loadConfig()
	{
		try {
			$this->config = parse_ini_file(realpath(__DIR__.'/config').'/config.ini', true);
			if (isset($this->config[$this->adapterName])) {
				foreach ($this->adapterOptions as $option) {
					if (isset($this->config[$this->adapterName][$option])) {
						$this->$option = $this->config[$this->adapterName][$option];
					} else {
						throw new \Exception(ucfirst($this->adapterName) . ' adapter error: option "' . $option . '" is required');
					}
				}
			} else {
				throw new \Exception(ucfirst($this->adapterName) . ' adapter is not configured');
			}
		} catch (\Exception $e) {
			// var_dump($e->getMessage());
		}
	}
	
	abstract public function loadResource($params);
	
	protected function setName($name)
	{
		$this->adapterName = $name;
	}
	
	protected function setOptions($options)
	{
		$this->adapterOptions = $options;
	}
	
	protected function setMapping($mapping)
	{
		$this->mapping = $mapping;
	}
	
	protected function normalizeFeed($data)
	{
		$result = [];
		foreach ($data as $item) {
			$result[] = $this->applyMapping($item);
		}
		return $result;
	}
	
	protected function applyMapping($item)
	{
		$result = [];
		foreach ($this->mapping as $internal => $external) {
			if (isset($item->$external)) {
				if ($internal == 'date') {
					$date = new \DateTime($item->$external);
					$result[$internal] = $date->format('d M Y @H:i');
				} else {
					$result[$internal] = $item->$external;
				}
			}
		}
		return $result;
	}
}