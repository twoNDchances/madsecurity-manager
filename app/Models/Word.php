<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'wordlist_id',
    ];

    // Belongs
    public function wordlist()
    {
        return $this->belongsTo(Wordlist::class, 'wordlist_id');
    }
}
