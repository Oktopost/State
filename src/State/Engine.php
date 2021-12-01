<?php
namespace State;


use State\Configuration\Parser;
use State\Configuration\KeyConfig;
use State\Configuration\StateConfig;
use State\Exceptions\StateException;


class Engine
{
	public const CHANGE_STATE_KEY	= '\0';
	
	
	/** @var IMachine */
	private $machine;
	
	/** @var Configuration\Setup */
	private $config;
	
	/** @var string */
	private $stateName;
	
	
	private function getArgs(string $char, string $key, KeyConfig $config): CallbackArgs
	{
		$args = new CallbackArgs();
		
		$args->Key			= $key;
		$args->Char			= $char;
		$args->NextState	= $config->NextState;
		$args->CurrentState	= $this->stateName;
		
		return $args;
	}
	
	private function stateExitCallback(?StateConfig $state = null)
	{
		if (!$state)
		{
			$state = $this->config->getState($this->stateName);
		}
		
		if (!$state->hasKeyConfig(self::CHANGE_STATE_KEY))
		{
			return;
		}
		
		$keyConfig = $state->getKeyConfig(self::CHANGE_STATE_KEY);
		$args = $this->getArgs(self::CHANGE_STATE_KEY, self::CHANGE_STATE_KEY, $keyConfig);
		
		$keyConfig->invoke($this->machine, $args);
	}
	
	private function transition(string $char): void
	{
		$key = $this->machine->resolveKey($char);
		$state = $this->config->getState($this->stateName);
		
		if (!$state->hasKeyConfig($key))
			return;
		
		$keyConfig = $state->getKeyConfig($key);
		$args = $this->getArgs($char, $key, $keyConfig);
		
		$keyConfig->invoke($this->machine, $args);
		
		if ($args->CurrentState != $args->NextState)
		{
			$this->stateExitCallback($state);
		}
		
		$this->stateName = $keyConfig->NextState;
	}
	
	
	/**
	 * @param IMachine|string $machine
	 */
	public function __construct($machine)
	{
		if (is_string($machine))
		{
			$machine = new $machine;
		}
		
		if (!($machine instanceof IMachine))
			throw new StateException('Passed object does not implement Machine');
		
		$this->machine		= $machine;
		$this->config		= Parser::parser($machine::config());
		$this->stateName	= $this->config->getInitialStateName();
	}
	
	public function getMachine(): IMachine
	{
		return $this->machine;
	}
	
	public function execute(string $input)
	{
		$length = strlen($input);
		
		for ($i = 0; $i < $length; $i++)
		{
			$this->transition($input[$i]);
		}
		
		$this->stateExitCallback();
		
		$this->machine->finalize();
		
		return $this->machine->state();
	}
}