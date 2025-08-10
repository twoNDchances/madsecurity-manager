<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ClearLivewireTmpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clear:livewire-tmp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove "livewire-tmp" directory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if (Storage::disk('local')->exists('livewire-tmp'))
        {
            Storage::disk('local')->deleteDirectory('livewire-tmp');
        }
        $this->info('Livewire Temporary Directory removed');
    }
}
