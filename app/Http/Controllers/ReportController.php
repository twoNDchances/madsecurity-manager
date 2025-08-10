<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Services\IdentificationService;
use App\Services\NotificationService;
use App\Validators\API\ReportValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    private function relationships()
    {
        return [
            'defender' => [
                'getDefender' => IdentificationService::important(),
            ],
            'rule' => 'getRule',
            'user' => [
                'getOwner' => IdentificationService::important(),
            ],
        ];
    }

    public function list(Request $request)
    {
        $reports = Report::query();
        if ($request->boolean('all'))
        {
            return $reports->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $reports->paginate($pageSize);
    }

    public function show($id)
    {
        $report = Report::findOrFail($id);
        IdentificationService::load($report, $this->relationships());
        return $report;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), ReportValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $validated['output'] = match (is_array($validated['output']))
        {
            true => $validated['output'],
            false => [$validated['output']],
        };
        $report = Report::create($validated);
        IdentificationService::load($report, $this->relationships());
        NotificationService::announce(
            'warning',
            'Report',
            'Defender ' . $validated['defender_id'] . ': Rule ' . $validated['rule_id'] . ' just received',
            true,
        );
        return $report;
    }

    public function delete($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();
        return response()->json([
            'message' => "Report $report->id deleted",
        ]);
    }
}
