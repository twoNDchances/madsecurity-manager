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
        'user_agent',
        'client_ip',
        'method',
        'path',
        'output',
        'target',
        'rule',

        'severity',
        'description',
        
        'wordlist_id',
        'user_id',
    ];

    protected $casts = [
        'phase' => 'integer',
        'inverse' => 'boolean',
        'log' => 'boolean',
        'time' => 'boolean',
        'user_agent' => 'boolean',
        'client_ip' => 'boolean',
        'method' => 'boolean',
        'path' => 'boolean',
        'output' => 'boolean',
        'target' => 'boolean',
        'rule' => 'boolean',
    ];

    protected $hidden = [
        'target_id',
        'wordlist_id',
        'user_id',
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

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'rules_groups')
        ->withPivot('position')
        ->orderBy('position');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'rule_id');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }
    
    // Businesses
    public static bool $skipObserver = false;
}
