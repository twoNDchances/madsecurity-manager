<?php

namespace App\Console\Commands;

use App\Models\Defender;
use App\Services\DefenderPreActionService;
use Illuminate\Console\Command;

class DefenderHealthCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'defender:health-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Health check Defenders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $defenders = Defender::all();
        $result = [];
        foreach ($defenders as $defender)
        {
            if (!$defender->periodic)
            {
                continue;
            }
            $data = [
                'id' => $defender->id,
                'last_status' => true,
            ];
            $response = DefenderPreActionService::request(
                $defender,
                $defender->health_method,
                "$defender->url$defender->health",
                false,
            );
            if (is_string($response) || !$response->successful())
            {
                $data['last_status'] = false;
                continue;
            }
            $result[] = $data;
        }
        foreach ($result as $defenderStatus)
        {
            $defender = Defender::find($defenderStatus['id']);
            if ($defender)
            {
                $defender->update(['last_status' => $defenderStatus['last_status']]);
            }
        }
    }
}
