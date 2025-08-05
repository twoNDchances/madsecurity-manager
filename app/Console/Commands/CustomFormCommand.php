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
     * The namespace of class.
     * 
     * @var string
     */
    private $namespace = 'App\Forms';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->createForm($name);
        $this->createAction($name);
    }

    private function createForm($name)
    {
        $path = app_path("Forms/{$name}Form.php");
        if (File::exists($path)) {
            $this->error("Form '{$name}' already exist!");
            return;
        }
        $content = <<<PHP
<?php

namespace $this->namespace;

use {$this->namespace}\Actions\\{$name}Action;
use App\Services\FilamentFormService;
use App\Services\TagFieldService;

class {$name}Form
{
    private static \$validator = null;

    private static \$action = {$name}Action::class;

    //

    public static function tags()
    {
        return TagFieldService::setTags();
    }
}

PHP;
        File::ensureDirectoryExists(app_path('Forms'));
        File::put($path, $content);
        $this->info("Form '{$name}' created at {$path}");
    }

    private function createAction($name)
    {
        $path = app_path("Forms/Actions/{$name}Action.php");
        if (File::exists($path)) {
            $this->error("Action '{$name}' already exist!");
            return;
        }
        $content = <<<PHP
<?php

namespace {$this->namespace}\Actions;

use Filament\Forms\Components\Actions\Action;

class {$name}Action
{
    //
}

PHP;
        File::ensureDirectoryExists(app_path('Forms/Actions'));
        File::put($path, $content);
        $this->info("Action '{$name}' created at {$path}");
    }
}
