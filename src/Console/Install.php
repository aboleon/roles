<?php

namespace Aboleon\Roles\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aboleon:roles {argument}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Aboleon Roles';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        if (method_exists($this, $this->argument('argument'))) {
            $this->{$this->argument('argument')}();
        } else {
            $this->error("Aboleon Roles: unknown console command '" . $this->argument('argument') . "'");
        }
    }

    protected function install()
    {
        $this->newLine();
        $this->info('Installing Aboleon Roles...');

        $this->publishConfigFile();
        $this->publishMigrations();
        $this->publishSeeders();

        $this->publishViews();
        $this->publishAssets();
        $this->publishRoutes();

        passthru('php artisan config:cache');
        passthru('php artisan optimize');

        $this->newLine();
        $this->info('Installed Aboleon Roles');

    }


    private function clearCaches(): void
    {
        $this->newLine();
        $this->comment('Updating caches...');
        $this->comment('------------------------------------------');
        $this->call('config:cache');
        $this->call('cache:clear');
        passthru('composer dump-autoload -o');
        passthru('php artisan optimize');
        //passthru('php artisan cache:clear');
    }

    private function publishMigrations(): void
    {

        $params = [
            '--provider' => "Aboleon\Roles\ServiceProvider",
            '--tag' => "aboleon-roles-migrations"
        ];

        $this->newLine();
        $this->comment("\n" . 'Publishing migrations...');
        $this->comment('------------------------------------------');
        $this->call('vendor:publish', $params);
        $this->info('Migrations in database/migrations/aboleon/roles');

        $this->newLine();
        $this->comment('Creating tables...');
        $this->comment('------------------------------------------');
         passthru('php artisan migrate --path=database/migrations/aboleon/roles');
      //  $this->call('migrate', ['--path' => 'database/migrations/aboleon/roles']);
        $this->info('Tables created. Rollback package migrations by running php artisan:rollback');
    }

    private function publishSeeders(): void
    {
        $this->newLine();
        $this->comment('Publishing seeders...');
        $this->comment('------------------------------------------');
      //  passthru('php artisan vendor:publish --provider=Aboleon\Roles\ServiceProvider --tag=aboleon-roles-seeders');

        $this->call('vendor:publish', [
            '--provider' => "Aboleon\Roles\ServiceProvider",
            '--tag' => "aboleon-roles-seeders"
        ]);
        $this->info('Seeders copied in database/seeders/Aboleon/Roles');


        $this->newLine();
        $this->comment('Seeding...');
        $this->comment('------------------------------------------');
        $this->call('db:seed', ['--class' => 'Database\Seeders\Aboleon\Roles\Seeder']);

        //$this->unsetUsersSeeder();

    }


    private function publishRoutes(): void
    {
        $this->newLine();
        $this->comment('Publishing route file...');
        $this->comment('------------------------------------------');

        passthru('php artisan vendor:publish --provider=Aboleon\Roles\ServiceProvider --tag=aboleon-roles-routes --force');

    }

    private function publishViews()
    {
        passthru('php artisan config:cache');

        $this->newLine();
        $this->comment('Publishing views...');
        $this->comment('------------------------------------------');


        if (file_exists(resource_path('views/welcome.blade.php'))) {
            $this->replaceInFile('/home', config('aboleon_publisher.route') . '/dashboard', resource_path('views/welcome.blade.php'));
            $this->replaceInFile('Home', 'Dashboard', resource_path('views/welcome.blade.php'));
        }

        passthru('php artisan vendor:publish --provider=Aboleon\Roles\ServiceProvider --tag=aboleon-roles-views --force');

    }


    private function publishAssets()
    {
        $this->newLine();
        $this->comment('Publishing assets...');
        $this->comment('------------------------------------------');
        $params = [
            '--provider' => "Aboleon\Roles\ServiceProvider",
            '--tag' => "aboleon-roles-assets"
        ];

        $this->call('vendor:publish', $params);
    }

    private function publishConfigFile()
    {
        $this->newLine();
        $this->comment('Publishing configuration...');
        $this->comment('------------------------------------------');
        $this->callPublishConfiguration();
    }


    private function configExists($fileName): bool
    {
        return File::exists(config_path($fileName));
    }

    private function shouldRemoveInstall(): bool
    {
        $this->alert('Are you sure to remove the Aboleon Roles ?');
        $this->comment('This will remove configuration files, migrations, database tables, application files and assets.');
        $this->comment('You can choose what to delete precisely, step by step, or remove it all at once.');
        return $this->confirm(
            'Proceed ?',
            false
        );
    }

    private function shouldRemoveInstallBySteps(): bool
    {
        return $this->confirm(
            'Do you want to remove it step by step ?',
            false
        );
    }

    private function callPublishConfiguration($forcePublish = false)
    {
        $params = [
            '--provider' => "Aboleon\Roles\ServiceProvider",
            '--tag' => "aboleon-roles-config"
        ];

        if ($forcePublish === true) {
            $params['--force'] = '';
        }

        $this->call('vendor:publish', $params);
    }


    /**
     * Replace a given string within a given file.
     *
     * @param string $search
     * @param string $replace
     * @param string $path
     * @return void
     */
    protected function replaceInFile($search, $replace, $path)
    {
        file_put_contents($path, str_replace($search, $replace, file_get_contents($path)));
    }


    protected function remove()
    {
        if ($this->shouldRemoveInstall()) {
            $this->info('Removing Aboleon Roles...');

            if ($this->shouldRemoveInstallBySteps()) {

                if ($this->confirm(
                    'Remove config file ?',
                    false
                )) {
                    File::delete(config_path('aboleon_publisher.php'));
                    $this->info(config_path('aboleon_publisher.php') . ' was deleted');
                }

                if ($this->confirm(
                    'Remove database migration files ?',
                    false
                )) {
                    File::deleteDirectory(database_path('migrations/aboleon/roles'));
                    $this->info('migrations/aboleon/roles was deleted');
                    File::deleteDirectory(database_path('seeders/Aboleon/Roles'));
                    $this->info(database_path('seeders/Aboleon/Roles was deleted'));
                }

                if ($this->confirm(
                    'Remove database tables ?',
                    false
                )) {
                    Schema::dropIfExists('aboleon_publisher_config');
                    $this->info('Table aboleon_publisher_config was deleted.');
                }

            } else {

                File::delete(config_path('aboleon_publisher.php'));
                File::deleteDirectory(database_path('migrations/aboleon/roles'));
                File::deleteDirectory(database_path('seeders/Aboleon/Roles'));
                Schema::dropIfExists('aboleon_publisher_config');
            }

            $this->info('Aboleon Roles was removed.');
            $this->clearCaches();

        } else {
            $this->info('Aboleon Roles was not removed.');
        }
    }


}