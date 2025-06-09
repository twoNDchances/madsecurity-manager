<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Composer extends Model
{
    use HasFactory;

    protected $fillable = [
        'yaml',
        'resources',
        'pass',
        'fall',
        'message',
        'user_id',
    ];

    protected $casts = [
        'resources' => 'array',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    // Businesses
    public static function getModels(array $except = ['Composer']): array
    {
        $modelsPath = app_path('Models');
        $files = glob("$modelsPath/*.php");
        $models = [];
        foreach ($files as $file) {
            $modelName = basename($file, '.php');
            if (!in_array($modelName, $except)) {
                $modelName .= 's';
                $models[Str::lower($modelName)] = $modelName;
            }
        }
        return $models;
    }
}
