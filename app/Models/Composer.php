<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Composer extends Model
{
    use HasFactory;

    protected $fillable = [
        'yaml',
        'resources',
        'pass',
        'fall',
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
}
