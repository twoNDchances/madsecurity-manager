<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Decision extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phase_type',
        'score',
        'action',
        'action_configuration',
        'wordlist_id',
        'user_id',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // Belongs
    public function getOwner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getWordlist()
    {
        return $this->belongsTo(Wordlist::class, 'wordlist_id');
    }

    // Relationships
    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    public function defenders()
    {
        return $this->belongsToMany(Defender::class, 'defenders_decisions');
    }
}
