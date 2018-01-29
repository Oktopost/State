<?php
namespace State;


interface IMachine
{
	public static function config();
	
	public function resolveKey(string $char): string;
	public function finalize(): void;
	
	public function state();
}