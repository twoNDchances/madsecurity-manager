<?php

namespace App\Http\Controllers;

use App\Models\Rule;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\RuleValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RuleController extends Controller
{
    private function relationships()
    {
        return [
            'group' => 'groups',
            'record' => 'records',
            'target' => 'getTarget',
            'wordlist' => 'getWordlist',
            'user' => [
                'getOwner' => IdentificationService::important(),
            ],
            'tag' => 'tags',
        ];
    }

    public function list(Request $request)
    {
        $rules = Rule::query();
        if ($request->boolean('all'))
        {
            return $rules->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $rules->paginate($pageSize);
    }

    public function show($id)
    {
        $rule = Rule::findOrFail($id);
        IdentificationService::load($rule, $this->relationships());
        return $rule;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), RuleValidator::build($request));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if ($validated['comparator'] == '@inRange')
        {
            $validated['value'] = implode(',', [$validated['from'], $validated['to']]);
        }
        if ($validated['action'])
        {
            $validated['action_configuration'] = match ($validated['action'])
            {
                'request' => implode(',', [$validated['request_method'], $validated['request_url']]),
                'setVariable' => implode(',', [$validated['variable_key'], $validated['variable_value']]),
                'setHeader' => implode(',', [$validated['header_key'], $validated['header_value']]),
                default => $validated['action_configuration'] ?? null,
            };
        }
        $rule = Rule::create($validated);
        if (isset($validated['group_ids']))
        {
            $rule->groups()->sync($validated['group_ids']);
        }
        TagFieldService::syncTags($validated, $rule);
        IdentificationService::load($rule, $this->relationships());
        return $rule;
    }

    public function update(Request $request, $id)
    {
        $rule = Rule::findOrFail($id);
        $validator = Validator::make($request->all(), RuleValidator::build(
            $request,
            false,
            $rule->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        if ($validated['comparator'] == '@inRange')
        {
            $validated['value'] = implode(',', [$validated['from'], $validated['to']]);
        }
        if ($validated['action'])
        {
            $validated['action_configuration'] = match ($validated['action'])
            {
                'request' => implode(',', [$validated['request_method'], $validated['request_url']]),
                'setVariable' => implode(',', [$validated['variable_key'], $validated['variable_value']]),
                'setHeader' => implode(',', [$validated['header_key'], $validated['header_value']]),
                default => $validated['action_configuration'] ?? null,
            };
        }
        $rule->update($validated);
        if (isset($validated['group_ids']))
        {
            $rule->groups()->sync($validated['group_ids']);
        }
        TagFieldService::syncTags($validated, $rule);
        IdentificationService::load($rule, $this->relationships());
        return $rule;
    }

    public function delete($id)
    {
        $rule = Rule::findOrFail($id);
        $rule->delete();
        return response()->json([
            'message' => "Rule $rule->id deleted",
        ]);
    }
}
