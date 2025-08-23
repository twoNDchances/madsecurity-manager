<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DefenderPeriodicCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defender:auto {status} {--minute=15}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Health check Defenders automatically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $status = $this->argument('status');
        $minute = (int) $this->option('minute');

        if (!in_array($status, ['true', 'false']))
        {
            $this->error("Status must be 'true' or 'false'");
            return;
        }

        if ($minute < 15)
        {
            $this->warn("Minute value too low, setting to 15 minutes");
            $minute = 15;
        }

        $artisan = base_path('artisan');
        $command = "*/{$minute} * * * * php {$artisan} defender:health-all >> /dev/null 2>&1";

        $process = Process::fromShellCommandline('crontab -l');
        $process->run();
        $currentCrons = $process->isSuccessful() ? $process->getOutput() : '';

        switch ($status)
        {
            case "true":
                if (strpos($currentCrons, $command) !== false)
                {
                    $this->warn('Command already exists in cronjob');
                    return;
                }
                $newCrons = trim($currentCrons . PHP_EOL . $command) . PHP_EOL;
                $this->setCron($newCrons);
                $this->info("Command added to cronjob, runs every {$minute} minutes");
                break;

            case "false":
                $newCrons = collect(explode(PHP_EOL, $currentCrons))
                ->reject(fn($line) => str_contains($line, "php {$artisan} defender:health-all"))
                ->implode(PHP_EOL) . PHP_EOL;
                $this->setCron($newCrons);
                $this->info('Command removed from cronjob');
                break;
        }
    }

    protected function setCron($cronContent)
    {
        $process = Process::fromShellCommandline('crontab -');
        $process->setInput($cronContent);
        $process->run();
    }
}
