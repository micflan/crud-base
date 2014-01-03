<?php namespace Micflan\CrudBase;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;

class CrudBaseServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('micflan/crud-base');

        AliasLoader::getInstance()->alias('CBRouter', 'Micflan\CrudBase\CrudBaseRouter');
        AliasLoader::getInstance()->alias('CBModel', 'Micflan\CrudBase\CrudBaseModel');
        AliasLoader::getInstance()->alias('CBController', 'Micflan\CrudBase\CrudBaseController');
        AliasLoader::getInstance()->alias('CBObserver', 'Micflan\CrudBase\CrudObserver');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array();
	}

}
