<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function create(Request $request)
    {
        $json = $request->json()->all();
        $data = [
            'defender_id' => $json['auth']['id'],
        ];
        foreach ($json['data'] as $key => $value)
        {
            $data[$key] = match ($key)
            {
                'output' => !is_array($value) ? [$value] : $value,
                'time' => Carbon::createFromFormat('H:i:s - d/m/Y', $value),
                default => $value,
            };
        }
        Report::create($data);
        NotificationService::announce(
            'warning',
            'Report',
            'Defender ' . $data['defender_id'] . ': Rule ' . $data['rule_id'] . ' just received',
            true,
        );
        return response()->json([
            'status' => true,
            'message' => 'created',
            'data' => null,
            'error' => null,
        ]);
    }
}
