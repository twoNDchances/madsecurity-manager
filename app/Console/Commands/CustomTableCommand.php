<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CustomTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:table {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a FilamentPHP Table class';

    /**
     * The namespace of class.
     * 
     * @var string
     */
    private $namespace = 'App\Tables';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $this->createTable($name);
        $this->createAction($name);
    }

    private function createTable($name)
    {
        $path = app_path("Tables/{$name}Table.php");
        if (File::exists($path)) {
            $this->error("Table '{$name}' already exist!");
            return;
        }
        $content = <<<PHP
<?php

namespace $this->namespace;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use App\Tables\Actions\\{$name}Action;

class {$name}Table
{
    private static \$action = {$name}Action::class;

    public static function tags()
    {
        return TagFieldService::getTags();
    }

    public static function owner()
    {
        return FilamentTableService::text('getOwner.email', 'Created by');
    }

    public static function actionGroup()
    {
        return self::\$action::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return self::\$action::deleteBulkAction();
    }
}

PHP;
        File::ensureDirectoryExists(app_path('Tables'));
        File::put($path, $content);
        $this->info("Table '{$name}' created at {$path}");
    }

    private function createAction($name)
    {
        $path = app_path("Tables/Actions/{$name}Action.php");
        if (File::exists($path)) {
            $this->error("Action '{$name}' already exist!");
            return;
        }
        $content = <<<PHP
<?php

namespace {$this->namespace}\Actions;

use App\Services\FilamentTableService;
use Filament\Tables\Actions\DeleteBulkAction;

class {$name}Action
{
    public static function actionGroup()
    {
        return FilamentTableService::actionGroup();
    }

    public static function deleteBulkAction()
    {
        return DeleteBulkAction::make();
    }
}

PHP;
        File::ensureDirectoryExists(app_path('Tables/Actions'));
        File::put($path, $content);
        $this->info("Action '{$name}' created at {$path}");
    }
}
