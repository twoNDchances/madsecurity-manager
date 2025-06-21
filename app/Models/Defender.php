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
        'status' => 'boolean',
        'output'=> 'array',
    ];

    protected $hidden = [
        'password',
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

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'defenders_groups');
    }
}
