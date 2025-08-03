<?php

namespace App\Http\Controllers;

use App\Models\Fingerprint;
use App\Services\IdentificationService;
use Illuminate\Http\Request;

class FingerprintController extends Controller
{
    private function relationships($user)
    {
        return [
            'user' => [
                'getOwner' => function($query) use ($user)
                {
                    if (!$user->important)
                    {
                        $query = $query->where('important', false);
                    }
                    return $query;
                },
            ],
        ];
    }

    public function list(Request $request)
    {
        $fingerprints = Fingerprint::query();
        if ($request->boolean('all'))
        {
            return $fingerprints->get();
        }
        $pageSize = $request->integer('pageSize', 10);
        return $fingerprints->paginate($pageSize);
    }

    public function show($id)
    {
        $fingerprint = Fingerprint::findOrFail($id);
        IdentificationService::load(
            $fingerprint,
            $this->relationships(IdentificationService::get()),
        );
        return $fingerprint;
    }

    public function delete($id)
    {
        $fingerprint = Fingerprint::findOrFail($id);
        $fingerprint->delete();
        return response()->json([
            'message' => "Fingerprint $fingerprint->id deleted"
        ]);
    }
}
