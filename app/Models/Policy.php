<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Policy extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'user_id'
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'policies_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'policies_users');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }

    // Businesses
}
