<?php
namespace State\Configuration;


use Objection\LiteSetup;
use Objection\LiteObject;

use State\CallbackArgs;


/**
 * @property string $FunctionName
 * @property array	$Arguments
 */
class CallbackConfig extends LiteObject
{
	/**
	 * @return array
	 */
	protected function _setup()
	{
		return [
			'FunctionName'	=> LiteSetup::createString(),
			'Arguments'		=> LiteSetup::createArray()
		];
	}
	
	
	/**
	 * CallbackConfig constructor.
	 * @param string $name
	 * @param array|mixed $args
	 */
	public function __construct(string $name, $args = [])
	{
		parent::__construct();
		
		$this->FunctionName = $name;
		$this->Arguments = is_array($args) ? $args : [$args];
	}
	
	
	public function invoke($object, CallbackArgs $state)
	{
		$passedArgs = array_merge([$state, $this->Arguments]);
		
		try
		{
			call_user_func_array([$object, $this->FunctionName], $passedArgs);
		}
		catch (\Throwable $t)
		{
			throw $t;
		}
	}
}