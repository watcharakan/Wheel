<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'place_id',
        'name',
        'address',
        'review_link',
        'contact_name',
        'contact_tel',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
