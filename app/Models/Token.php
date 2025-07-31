<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'key',
        'value',
        'located_at',
        'description',
        'expired_at',
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

    public function users()
    {
        return $this->belongsToMany(User::class, 'tokens_users');
    }

    // Businesses
    public static bool $skipObserver = false;
}
