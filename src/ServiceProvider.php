<?php

namespace Aboleon\Roles;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Aboleon\Roles\Console\Install;
use Illuminate\View\Compilers\BladeCompiler;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {

        Gate::before(function ($user) {
            if ($user->hasRole('dev')) {
                return true;
            }
        });



        Blade::directive('role', function ($arguments) {

            return "<?php if (auth()->check() && auth()->user()->hasRole({$arguments})) { ?>";
        });

        Blade::directive('endrole', function () {
            return "<?php } ?>";
        });



        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'aboleon.roles');
        $this->loadTranslationsFrom(__DIR__ . '/Resources/lang', 'aboleon.roles');
/*
        if ($this->app->runningInConsole()) {

            $this->publishConfig();
            $this->commands([
                Install::class,
            ]);
        }
*/
    }

    private function publishConfig()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('aboleon_roles.php'),
        ], 'aboleon-roles-config');
    }

    private function publishMigrations()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/01_create_permissions_table.php.stub' => database_path('migrations/aboleon/roles/' . date('Y_m_d') . '_0000001_create_permissions_table.php'),
            __DIR__ . '/../database/migrations/02_create_roles_table.php.stub' => database_path('migrations/aboleon/roles/' . date('Y_m_d') . '_0000002_create_roles_table.php'),
            __DIR__ . '/../database/migrations/03_create_roles_permissions_table.php.stub' => database_path('migrations/aboleon/roles/' . date('Y_m_d') . '_0000003_create_roles_permissions_table.php'),
            __DIR__ . '/../database/migrations/04_create_users_permissions_table.php.stub' => database_path('migrations/aboleon/roles/' . date('Y_m_d') . '_0000004_create_users_permissions_table.php'),
            __DIR__ . '/../database/migrations/05_create_user_roles_description_table.php.stub' => database_path('migrations/aboleon/roles/' . date('Y_m_d') . '_0000005_create_user_roles_description_table.php'),
        ], 'aboleon-roles-migrations');
    }

    private function publishSeeders()
    {
        $this->publishes([
            __DIR__ . '/../database/seeders/Seeder.php' => database_path('seeders/Aboleon/Roles/Seeder.php'),
        ], 'aboleon-roles-seeders');
    }


    private function publishAssets(): void
    {
        $this->publishes([
            __DIR__ . '/../publishables/' => public_path('aboleon/roles/'),
        ], 'aboleon-roles-assets');
    }
}