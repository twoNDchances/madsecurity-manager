<?php

namespace App\Http\Controllers;

use App\Models\Fingerprint;
use App\Services\IdentificationService;
use Illuminate\Http\Request;

class FingerprintController extends Controller
{
    public function list(Request $request)
    {
        $pageSize = $request->integer('pageSize', 10);
        return Fingerprint::paginate($pageSize);
    }

    public function show($id)
    {
        $fingerprint = Fingerprint::findOrFail($id);
        IdentificationService::load($fingerprint, [
            'user' => 'getOwner',
        ]);
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
