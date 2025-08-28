<?php

namespace App\Http\Controllers;

use App\Models\Defender;
use App\Services\DefenderApplyService;
use App\Services\DefenderHealthService;
use App\Services\DefenderImplementService;
use App\Services\DefenderRevokeService;
use App\Services\DefenderSuspendService;
use App\Services\FingerprintService;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\DefenderValidator;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DefenderController extends Controller
{
    private function relationships()
    {
        return [
            'decision' => 'decisions',
            'group' => 'groups',
            'report' => 'reports',
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

    private function decodeBase64($base64)
    {
        if (is_string($base64) && str_contains($base64, 'base64,')) {
            $base64 = explode('base64,', $base64, 2)[1];
        }
        $pem = base64_decode($base64, true);
        $fileName = (string) Str::uuid() . '.crt';
        $relativePath = "tls/$fileName";
        Storage::disk('local')->put($relativePath, $pem);
        return $relativePath;
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
        if (isset($validated['certification']))
        {
            $validated['certification'] = $this->decodeBase64($validated['certification']);
        }
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
        if (isset($validated['certification']) && $validated['certification'] != null)
        {
            $validated['certification'] = $this->decodeBase64($validated['certification']);
        }
        if (!$validated['protection'])
        {
            $validated['username'] = $validated['password'] = null;
        }
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

    private function returnOutput(Defender $defender)
    {
        return response()->json([
            'output' => array_filter(
                array_map(
                    'trim',
                    explode("\n", $defender->output),
                ),
            ),
        ]);
    }

    public function health($id)
    {
        $defender = Defender::findOrFail($id);
        $defender = DefenderHealthService::perform($defender, false);
        FingerprintService::generate($defender, 'Check Health');
        return $this->returnOutput($defender);
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

    public function apply(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'group_ids' => 'sometimes|array',
            'group_ids.*' => 'exists:groups,id',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['group_ids']))
        {
            $groups = $defender->groups()->whereIn('id', $validated['group_ids']);
            $defender = DefenderApplyService::performSpecific($groups, $defender, false);
            FingerprintService::generate($defender, 'Apply');
        }
        else
        {
            $defender = DefenderApplyService::performAll($defender, false);
            FingerprintService::generate($defender, 'Apply All');
        }
        return $this->returnOutput($defender);
    }

    public function revoke(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'group_ids' => 'sometimes|array',
            'group_ids.*' => 'exists:groups,id',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['group_ids']))
        {
            $groups = $defender->groups()->whereIn('id', $validated['group_ids']);
            $defender = DefenderRevokeService::performSpecific($groups, $defender, false);
            FingerprintService::generate($defender, 'Revoke');
        }
        else
        {
            $defender = DefenderRevokeService::performAll($defender, false);
            FingerprintService::generate($defender, 'Revoke All');
        }
        return $this->returnOutput($defender);
    }

    public function implement(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'decision_ids' => 'sometimes|array',
            'decision_ids.*' => 'exists:decisions,id',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['decision_ids']))
        {
            $decisions = $defender->decisions()->whereIn('id', $validated['decision_ids']);
            $defender = DefenderImplementService::performSpecific($decisions, $defender, false);
            FingerprintService::generate($defender, 'Implement');
        }
        else
        {
            $defender = DefenderImplementService::performAll($defender, false);
            FingerprintService::generate($defender, 'Implement All');
        }
        return $this->returnOutput($defender);
    }

    public function suspend(Request $request, $id)
    {
        $defender = Defender::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'decision_ids' => 'sometimes|array',
            'decision_ids.*' => 'exists:decisions,id',
        ]);
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if (isset($validated['decision_ids']))
        {
            $decisions = $defender->decisions()->whereIn('id', $validated['decision_ids']);
            $defender = DefenderSuspendService::performSpecific($decisions, $defender, false);
            FingerprintService::generate($defender, 'Suspend');
        }
        else
        {
            $defender = DefenderSuspendService::performAll($defender, false);
            FingerprintService::generate($defender, 'Suspend All');
        }
        return $this->returnOutput($defender);
    }
}
