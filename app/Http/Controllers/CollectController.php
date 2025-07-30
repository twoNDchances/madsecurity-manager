<?php

namespace App\Http\Controllers;

use App\Models\Defender;
use Illuminate\Http\Request;

class CollectController extends Controller
{
    public function collect(Request $request)
    {
        $defender = Defender::find($request->query('id'));
        if (!$defender) {
            abort(404);
        }
        $defender->load([
            'groups',
            'groups.rules',
            'groups.rules.getWordlist',
            'groups.rules.getWordlist.words',
            'groups.rules.getTarget',
            'groups.rules.getTarget.getWordlist',
            'groups.rules.getTarget.getWordlist.words',
        ]);
        $data = json_encode($defender->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return response($data)
        ->header('Content-Type', 'application/json')
        ->header('Content-Disposition', "attachment; filename=\"defender_$defender->id.json\"");
    }
}
