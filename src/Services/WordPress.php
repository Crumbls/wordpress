<?php

namespace Crumbls\WordPress\Services;

use Crumbls\WordPress\Contracts\WordPressDriver;
use Illuminate\Foundation\Application;
use Illuminate\Support\Manager;

class WordPress extends Manager {
	/**
	 * Get the default driver name.
	 *
	 * @return string
	 */
	public function getDefaultDriver()
	{
		return $this->config->get('services.wordpress.driver');
	}
}