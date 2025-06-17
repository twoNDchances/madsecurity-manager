<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomFormCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:form {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a FilamentPHP Form class';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Forms/{$name}.php");

        if (File::exists($path)) {
            $this->error("Form '{$name}' already exist!");
            return;
        }

        $namespace = 'App\Forms';
        $content = <<<PHP
<?php

namespace $namespace;

use App\Services\FilamentFormService;
use App\Services\TagFieldService;

class $name
{
    private static \$validator = null;
    //
}

PHP;
        File::ensureDirectoryExists(app_path('Forms'));
        File::put($path, $content);

        $this->info("Form '{$name}' created at {$path}");
    }
}
