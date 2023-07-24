<?php

namespace Crumbls\WordPress\Facades;

use Illuminate\Support\Facades\Facade;

class WordPressFacade extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'wordpress';
	}
}