<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'path',
        'output',
        'total_asset',
        'total_resource',
        'fail_resource',
        'user_id'
    ];

    protected $casts = [
        'total_asset' => 'integer',
        'total_resource' => 'integer',
        'fail_resource' => 'integer',
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

    public function fingerprints()
    {
        return $this->morphMany(Fingerprint::class, 'resource');
    }

    // Businesses
    public static bool $skipObserver = false;
}
