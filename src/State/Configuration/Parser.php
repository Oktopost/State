<?php
namespace State\Configuration;


use State\Exceptions\StateException;
use Traitor\TStaticClass;


class Parser
{
	use TStaticClass;
	
	
	/**
	 * @param string|array $data
	 * @return CallbackConfig[]
	 */
	public static function parseCallbacks($data): array
	{
		if (is_string($data))
		{
			return [new CallbackConfig($data)];
		}
		else if (is_array($data))
		{
			$result = [];
			
			foreach ($data as $key => $record)
			{
				if (is_string($key))
				{
					$result[] = new CallbackConfig($key, $record);
				}
				else
				{
					$result[] = new CallbackConfig($record);
				}
			}
			
			return $result;
		}
		else
		{
			throw new StateException('Unexpected configuration');
		}
	}
	
	public static function parseKeyConfig(string $state, $key, array $data): KeyConfig
	{
		if (!is_string($key))
			$key = $data['key'] ?? '*';
		
		if (!is_array($key))
		{
			if (strpos($key, ',') !== false)
			{
				$key = explode(',', $key);
			}
			else
			{
				$key = [$key];
			}
		}
		
		$nextState = $data['state'] ?? $state;
		
		$callbacks = $data['callbacks'] ?? $data['callback'] ?? [];
		$callbacks = self::parseCallbacks($callbacks);
		
		$config = new KeyConfig($key, $state, $nextState);
		$config->Callbacks = $callbacks;
		
		return $config;
	}
	
	public static function parseStateConfig(string $state, array $data): StateConfig
	{
		$config = new StateConfig($state);
		
		foreach ($data as $key => $keySetup)
		{
			$config->setKeyConfig(self::parseKeyConfig($state, $key, $keySetup));
		}
		
		return $config;
	}
	
	
	public static function parser(array $config): Setup
	{
		$setup = new Setup();
		
		foreach ($config as $key => $value)
		{
			$setup->setState(self::parseStateConfig($key, $value));
			
			if (($value['start'] ?? false))
			{
				$setup->setInitialStateName($key);
			}
		}
		
		return $setup;
	}
}