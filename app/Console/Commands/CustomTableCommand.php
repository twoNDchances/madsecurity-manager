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
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('name');
        $path = app_path("Tables/{$name}.php");

        if (File::exists($path)) {
            $this->error("Table '{$name}' already exist!");
            return;
        }

        $namespace = 'App\Tables';
        $content = <<<PHP
<?php

namespace $namespace;

use App\Services\FilamentTableService;
use App\Services\TagFieldService;
use Filament\Tables\Actions\DeleteBulkAction;

class $name
{
    public static function tags()
    {
        return TagFieldService::getTags();
    }

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
        File::ensureDirectoryExists(app_path('Tables'));
        File::put($path, $content);

        $this->info("Table '{$name}' created at {$path}");
    }
}
