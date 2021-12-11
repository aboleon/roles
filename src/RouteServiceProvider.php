<?php

namespace Aboleon\Roles;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The root namespace to assume when generating URLs to actions.
     *
     * @var string
     */
    protected $defer = false;
    protected $namespace = '\Aboleon\Roles\Http\Controllers';
    public const HOME = '/';

    /**
     * Called before routes are registered.
     *
     * Register any model bindings or pattern based filters.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapWebRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::prefix(config('aboleon_roles.route', 'aboleon/roles'))
            ->middleware(['web','auth:sanctum'])
            ->namespace($this->namespace)
            ->name('aboleon.roles.')
            ->group(function () {
                include __DIR__ . '/Routes/web.php';
            });
    }
}
