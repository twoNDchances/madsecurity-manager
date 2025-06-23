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
        'sync',
        'apply',
        'revoke',
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
        'output' => 'array',
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
        return $this->belongsToMany(Group::class, 'defenders_groups');
    }
}
