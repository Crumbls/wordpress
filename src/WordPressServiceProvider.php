<?php

namespace Crumbls\WordPress;

use Crumbls\WordPress\Drivers\RestRepository;
use Crumbls\WordPress\Facades\WordPressFacade;
use Crumbls\WordPress\Services\WordPress;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Foundation\AliasLoader;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\Foundation\Console\AboutCommand;


class WordPressServiceProvider extends ServiceProvider
{
	/**
	 * Boot our package.
	 */
    public function boot()
    {
    }

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register() : void {
		$this->app->bind('wordpress', function()  {
			$ret = new WordPress(app());

			$ret->extend('wp-json', function ($app) {
				return new RestRepository($app);
			});

			return $ret;
		});


		$loader = \Illuminate\Foundation\AliasLoader::getInstance();
		$loader->alias('WordPress', WordPressFacade::class);
	}
}