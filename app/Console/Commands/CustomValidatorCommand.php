<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomValidatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:validator {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Validator class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Validators/{$name}.php");

        if (File::exists($path)) {
            $this->error("Validator '{$name}' already exist!");
            return;
        }

        $namespace = 'App\Validators';
        $content = <<<PHP
<?php

namespace $namespace;

class $name
{
    //
}

PHP;
        File::ensureDirectoryExists(app_path('Validators'));
        File::put($path, $content);

        $this->info("Validator '{$name}' created at {$path}");
    }
}
