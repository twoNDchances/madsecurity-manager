<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeServiceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:service {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a service class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Services/{$name}.php");

        if (File::exists($path)) {
            $this->error("Service '{$name}' already exist!");
            return;
        }

        $namespace = 'App\Services';
        $content = <<<PHP
<?php

namespace $namespace;

class $name
{
    //
}

PHP;
        File::ensureDirectoryExists(app_path('Services'));
        File::put($path, $content);

        $this->info("Service '{$name}' created at {$path}");
    }
}
