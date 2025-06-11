<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    use HasFactory;

    protected $fillable = [
        'alias',
        'type',
        'name',
        // 'real_name',
        'phase',
        'datatype',
        'final_datatype',
        'engine',
        'engine_configuration',
        'description',
        'immutable',
        'user_id',
        'target_id',
        'wordlist_id',
    ];

    protected $casts = [
        'phase' => 'integer',
        'immutable' => 'boolean',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class);
    }

    public function getSuperior()
    {
        return $this->belongsTo(Target::class, 'target_id');
    }

    public function getSubordinates()
    {
        return $this->hasMany(Target::class, 'target_id');
    }

    public function getWordlist()
    {
        return $this->belongsTo(Wordlist::class, 'wordlist_id');
    }

    // Relationships
    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    // Businesses
    public static function getRoot(Target $target)
    {
        if (!$target->target_id)
        {
            return $target;
        }
        $superior = $target->getSuperior;
        if (!$superior)
        {
            return $target;
        }
        return self::getRoot($superior);
    }
}