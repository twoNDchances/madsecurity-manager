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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function words()
    {
        return $this->hasMany(Word::class, 'wordlist_id');
    }
}
