<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wordlist extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'alias',
        'description',
        'user_id'
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relationships
    public function words()
    {
        return $this->hasMany(Word::class, 'wordlist_id');
    }

    public function targets()
    {
        return $this->belongsToMany(Target::class, 'wordlists_targets');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class,'taggable');
    }
}
