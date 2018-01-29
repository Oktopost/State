<?php
namespace State\Configuration;


use Objection\LiteSetup;
use Objection\LiteObject;

use State\Exceptions\StateException;


/**
 * @property string 		$ID
 * @property KeyConfig[]	$StepByKey
 */
class StateConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'ID'		=> LiteSetup::createString(null),
			'StepByKey'	=> LiteSetup::createInstanceArray(KeyConfig::class)
		];
	}
	
	
	public function __construct(string $id)
	{
		parent::__construct();
		$this->ID = $id;
	}
	
	
	public function hasKeyConfig(string $key): bool
	{
		return isset($this->StepByKey[$key]);
	}
	
	public function getKeyConfig(string $key): ?KeyConfig
	{
		if (!isset($this->StepByKey[$key]))
			throw new StateException("Key '{$key}' is not expected in state '{$this->ID}'");
		
		return $this->StepByKey[$key];
	}
	
	/**
	 * @param KeyConfig $config
	 */
	public function setKeyConfig(KeyConfig $config): void
	{
		foreach ($config->Keys as $key)
		{
			if (isset($this->StepByKey[$key]))
				throw new StateException("Duplicate configuration for key '{$key}' is state '{$this->ID}'");
		
			$this->StepByKey[$key] = $config;
		}
	}
}