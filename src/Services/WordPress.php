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
		dd(__LINE__);
		return $this->config->get('cart.default');
	}


	public function getPosts($perPage = 10) : array {
		return $this->driver->getPosts($perPage);
	}

	public function getPostTypes(int $perPage = 10) : array {
		return $this->driver->getPostTypes($perPage);
	}

	public function getCategories(int $perPage = 10): array
	{
		// TODO: Implement getCategories() method.
	}

	public function getComments(int $perPage = 10): array
	{
		// TODO: Implement getComments() method.
	}

	public function getMedia(int $perPage = 10): array
	{
		// TODO: Implement getMedia() method.
	}

	public function getPages(int $perPage = 10): array
	{
		// TODO: Implement getPages() method.
	}

	public function getUsers(int $perPage = 10): array
	{
		// TODO: Implement getUsers() method.
	}
}