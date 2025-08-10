<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Services\IdentificationService;
use App\Services\NotificationService;
use App\Validators\API\RecordValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecordController extends Controller
{
    private function relationships()
    {
        return [
            'defender' => [
                'getDefender' => IdentificationService::important(),
            ],
            'rule' => 'getRule',
        ];
    }

    public function list(Request $request)
    {
        $records = Record::query();
        if ($request->boolean('all'))
        {
            return $records->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $records->paginate($pageSize);
    }

    public function show($id)
    {
        $record = Record::findOrFail($id);
        IdentificationService::load($record, $this->relationships());
        return $record;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), RecordValidator::build());
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
        $record = Record::create($validated);
        IdentificationService::load($record, $this->relationships());
        NotificationService::announce(
            'warning',
            'Record',
            'Defender ' . $validated['defender_id'] . ': Rule ' . $validated['rule_id'] . ' just received',
            true,
        );
        return $record;
    }

    public function delete($id)
    {
        $record = Record::findOrFail($id);
        $record->delete();
        return response()->json([
            'message' => "Record $record->id deleted",
        ]);
    }
}
