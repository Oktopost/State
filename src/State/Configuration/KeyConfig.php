<?php
namespace State\Configuration;


use Objection\LiteSetup;
use Objection\LiteObject;

use State\CallbackArgs;


/**
 * @property string[]			$Keys
 * @property string				$State
 * @property string				$NextState
 * @property CallbackConfig[]	$Callbacks
 */
class KeyConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'Keys'			=> LiteSetup::createArray(),
			'State'			=> LiteSetup::createString(),
			'NextState'		=> LiteSetup::createString(),
			'Callbacks'		=> LiteSetup::createInstanceArray(CallbackConfig::class)
		];
	}
	
	
	public function __construct($keys, string $state, ?string $next = null)
	{
		parent::__construct();
		
		$this->Keys = is_array($keys) ? $keys : [$keys];
		$this->State = $state;
		$this->NextState = $next ?: $state; 
	}
	
	
	public function invoke($object, CallbackArgs $args)
	{
		foreach ($this->Callbacks as $callback)
		{
			$callback->invoke($object, $args);
		}
	}
}