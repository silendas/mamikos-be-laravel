<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'kost_id',
        'message',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kost()
    {
        return $this->belongsTo(Kost::class, 'kost_id');
    }
}
