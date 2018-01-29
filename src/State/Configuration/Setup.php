<?php
namespace State\Configuration;


use State\Exceptions\StateException;
use Structura\Arrays;


class Setup
{
	private $initialStateName;
	
	/** @var StateConfig[] */
	private $states = [];
	
	
	public function getState(string $id): StateConfig
	{
		if (!$this->states[$id])
			throw new StateException("State with ID '$id' does not exist");
		
		return $this->states[$id];
	}
	
	public function setState(StateConfig $singleState): void
	{
		$id = $singleState->ID;
		
		if (isset($this->states[$id]))
			throw new StateException("State with ID '$id' already exists");
		
		$this->states[$id] = $singleState;
	}
	
	public function setInitialStateName(string $state): void
	{
		$this->initialStateName = $state;
	}
	
	public function getInitialStateName(): string 
	{
		if (!$this->initialStateName)
		{
			if (count($this->states) == 0)
				throw new StateException('No states were configured!');
			
			$state = Arrays::first($this->states);
			$this->initialStateName = $state->ID;
		}
		
		return $this->initialStateName;
	}
}