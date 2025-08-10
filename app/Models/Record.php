<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Record extends Model
{
    use HasFactory;

    protected $fillable = [
        'defender_id',
        'time',
        'output',
        'user_agent',
        'client_ip',
        'method',
        'path',
        'target_ids',
        'rule_id',
    ];

    protected $casts = [
        'output' => 'array',
        'target_ids' => 'array',
    ];

    protected $hidden = [
        'defender_id',
        'rule_id',
    ];

    // Belongs
    public function getDefender()
    {
        return $this->belongsTo(Defender::class, 'defender_id');
    }

    public function getRule()
    {
        return $this->belongsTo(Rule::class, 'rule_id');
    }

    // Relationships
    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static bool $skipObserver = false;
}
