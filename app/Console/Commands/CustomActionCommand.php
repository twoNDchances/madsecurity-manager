<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomActionCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:action {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a FilamentPHP Action class';

    /**
     * The namespace of class.
     * 
     * @var string
     */
    private $namespace = 'App\Actions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Actions/{$name}Action.php");
        if (File::exists($path)) {
            $this->error("Action '{$name}' already exist!");
            return;
        }
        $content = <<<PHP
<?php

namespace $this->namespace;

use Filament\Actions\Action;

class {$name}Action
{
    //
}

PHP;
        File::ensureDirectoryExists(app_path('Actions'));
        File::put($path, $content);
        $this->info("Action '{$name}' created at {$path}");
    }
}
