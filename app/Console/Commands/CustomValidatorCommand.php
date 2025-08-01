<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CustomValidatorCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:validator {name} {--type=gui}';

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
        $type = Str::upper($this->option('type') ?? 'gui');
        if (!in_array($type, ['GUI', 'API']))
        {
            $this->error("Invalid type specified. Use 'gui' or 'api'!");
            return;
        }
        $path = app_path("Validators/{$type}/{$name}Validator.php");

        if (File::exists($path)) {
            $this->error("Validator '{$type}/{$name}' already exist!");
            return;
        }

        $namespace = "App\Validators\\$type";
        $content = <<<PHP
<?php

namespace $namespace;

class {$name}Validator
{
    //
}

PHP;
        File::ensureDirectoryExists(app_path("Validators/{$type}"));
        File::put($path, $content);

        $this->info("Validator '{$type}/{$name}' created at {$path}");
    }
}
