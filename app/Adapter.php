<?php 
namespace Test\App;

abstract class Adapter
{
	protected $mapping = [];
	
	abstract public function loadResource($params);
	
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