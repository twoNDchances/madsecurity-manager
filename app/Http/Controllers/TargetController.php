<?php

namespace App\Http\Controllers;

use App\Models\Target;
use App\Services\IdentificationService;
use App\Services\TagFieldService;
use App\Validators\API\TargetValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TargetController extends Controller
{
    private function relationships()
    {
        return [
            'rule' => 'rules',
            'wordlist' => 'getWordlist',
            'user' => [
                'getOwner' => IdentificationService::important(),
            ],
            'tag' => 'tags',
        ];
    }

    public function list(Request $request)
    {
        $targets = Target::query();
        if ($request->boolean('all'))
        {
            return $targets->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $targets->paginate($pageSize);
    }

    public function show($id)
    {
        $target = Target::findOrFail($id);
        IdentificationService::load($target, $this->relationships());
        return $target;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), TargetValidator::build($request));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $validated['final_datatype'] = match ($validated['engine'])
        {
            'indexOf' => 'string',
            'length' => 'number',
            default => $validated['datatype'],
        };
        $validated['engine_configuration'] = match ($validated['engine'])
        {
            'indexOf' => $validated['indexOf'],
            'addition',
            'subtraction',
            'multiplication',
            'division',
            'powerOf',
            'remainder' => $validated['number'],
            'hash' => $validated['hash'],
            default => null,
        };
        $validated['target_id'] = match ($validated['type'])
        {
            'target' => $validated['superior'],
            default => null,
        };
        $target = Target::create($validated);
        TagFieldService::syncTags($validated, $target);
        IdentificationService::load($target, $this->relationships());
        return $target;
    }

    public function update(Request $request, $id)
    {
        $target = Target::findOrFail($id);
        if ($target->immutable)
        {
            abort(404);
        }
        $validator = Validator::make($request->all(), TargetValidator::build(
            $request,
            false,
            $target->id,
        ));
        if ($validator->fails())
        {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 400);
        }
        $validated = $validator->validated();
        $validated['final_datatype'] = match ($validated['engine'])
        {
            'indexOf' => 'string',
            'length' => 'number',
            default => $validated['datatype'],
        };
        $validated['engine_configuration'] = match ($validated['engine'])
        {
            'indexOf' => $validated['indexOf'],
            'addition',
            'subtraction',
            'multiplication',
            'division',
            'powerOf',
            'remainder' => $validated['number'],
            'hash' => $validated['hash'],
            default => null,
        };
        $validated['target_id'] = match ($validated['type'])
        {
            'target' => $validated['superior'],
            default => null,
        };
        $target->update($validated);
        TagFieldService::syncTags($validated, $target);
        IdentificationService::load($target, $this->relationships());
        return $target;
    }

    public function delete($id)
    {
        $target = Target::findOrFail($id);
        if ($target->immutable)
        {
            abort(404);
        }
        $target->delete();
        return response()->json([
            'message'=> "Target $target->id deleted",
        ]);
    }
}
