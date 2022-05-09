<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_logins extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'ip',
        'countryName',
        'countryCode',
        'cityName',
        'user_id',
    ];
}
