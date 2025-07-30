<?php

namespace App\Models;

use App\Services\FingerprintService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Defender extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'important',
        'periodic',
        'last_status',
        'health',
        'health_method',
        'apply',
        'apply_method',
        'revoke',
        'revoke_method',
        'implement',
        'implement_method',
        'suspend',
        'suspend_method',
        'output',
        'description',
        'protection',
        'username',
        'password',
        'user_id',
    ];

    protected $casts = [
        'important' => 'boolean',
        'periodic' => 'boolean',
        'last_status' => 'boolean',
        'protection' => 'boolean',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'defenders_groups')
        ->withPivot('status');
    }

    public function decisions()
    {
        return $this->belongsToMany(Decision::class, 'defenders_decisions')
        ->withPivot('status');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'defender_id');
    }

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static function booting()
    {
        static::created(function($defender) 
        {
            FingerprintService::generate(
                $defender,
                'Create',
            );
        });

        static::updated(function($defender) 
        {
            FingerprintService::generate(
                $defender,
                'Update',
            );
        });

        static::deleted(function($defender) 
        {
            FingerprintService::generate(
                $defender,
                'Deleted',
            );
        });
    }
}
