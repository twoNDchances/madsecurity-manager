<?php

namespace App\Http\Controllers;

use App\Models\Defender;
use App\Services\FingerprintService;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\DefenderValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DefenderController extends Controller
{
    private function relationships()
    {
        return [
            'decision' => 'decisions',
            'group' => 'groups',
            'record' => 'records',
            'tag' => 'tags',
            'user' => [
                'getOwner' => IdentificationService::important(),
            ],
        ];
    }

    public function list(Request $request)
    {
        $user = IdentificationService::get();
        $defenders = Defender::query();
        if (!$user->important)
        {
            $defenders->where('important', false);
        }
        if ($request->boolean('all'))
        {
            return $defenders->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $defenders->paginate($pageSize);
    }

    public function show($id)
    {
        $defender = Defender::findOrFail($id);
        $user = IdentificationService::get();
        if ($defender->important && !$user->important)
        {
            abort(403);
        }
        IdentificationService::load($defender, $this->relationships());
        return $defender;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), DefenderValidator::build());
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $defender = Defender::create($validated);
        if (isset($validated['group_ids']))
        {
            $defender->groups()->sync($validated['group_ids']);
        }
        if (isset($validated['decision_ids']))
        {
            $defender->decisions()->sync($validated['decision_ids']);
        }
        TagFieldService::syncTags($validated, $defender);
        IdentificationService::load($defender, $this->relationships());
        return $defender;
    }

    public function update(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), DefenderValidator::build(
            false,
            $defender->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $defender->update($validated);
        if (isset($validated['group_ids']))
        {
            $defender->groups()->sync($validated['group_ids']);
        }
        if (isset($validated['decision_ids']))
        {
            $defender->decisions()->sync($validated['decision_ids']);
        }
        TagFieldService::syncTags($validated, $defender);
        IdentificationService::load($defender, $this->relationships());
        return $defender;
    }

    public function delete($id)
    {
        $defender = Defender::findOrFail($id);
        $defender->delete();
        return response()->json([
            'message' => "Defender $defender->id deleted"
        ]);
    }

    public function health($id)
    {
        return response()->json([]);
    }

    public function collect($id)
    {
        $defender = Defender::findOrFail($id);
        $relationship = [
            'decision' => 'decisions',
            'group' => 'groups',
            'rule' => 'groups.rules',
            'target' => 'groups.rules.getTarget',
            'wordlist' => [
                'groups.rules.getWordlist',
                'groups.rules.getWordlist.words',
                'groups.rules.getTarget.getWordlist',
                'groups.rules.getTarget.getWordlist.words',
            ],
        ];
        IdentificationService::load($defender, $relationship);
        $data = json_encode($defender->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        FingerprintService::generate($defender, 'Collect All');
        return response($data)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', "attachment; filename=\"defender_$defender->id.json\"");
    }

    public function apply($id)
    {
        //
    }

    public function revoke($id)
    {
        //
    }

    public function implement($id)
    {
        //
    }

    public function suspend($id)
    {
        //
    }
}
