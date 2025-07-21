<?php

namespace App\Models;

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
        'sync',
        'sync_method',
        'apply',
        'apply_method',
        'revoke',
        'revoke_method',
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
        return $this->belongsToMany(Decision::class, 'defenders_decisions');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'defender_id');
    }

    // Businesses
}
