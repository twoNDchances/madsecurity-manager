<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'value',
        'description',
        'expired_at',
        'user_id'
    ];

    protected $hidden = [
        'value',
        'user_id',
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
