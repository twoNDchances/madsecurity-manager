<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
        'phase',

        'target_id',
        'comparator',
        'inverse',
        'value',
        'action',
        'action_configuration',

        'log',
        'time',
        'status',
        'user_agent',
        'client_ip',
        'method',
        'path',

        'severity',
        'description',
        
        'wordlist_id',
        'user_id',
    ];

    protected $casts = [
        'inverse' => 'boolean',
        'log' => 'boolean',
        'time' => 'boolean',
        'status' => 'boolean',
        'user_agent' => 'boolean',
        'client_ip' => 'boolean',
        'method' => 'boolean',
        'path' => 'boolean',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTarget()
    {
        return $this->belongsTo(Target::class, 'target_id');
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
}
