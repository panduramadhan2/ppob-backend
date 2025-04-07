<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSaldo extends Model
{
    use HasFactory;

    protected $table = 'user_saldo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'user_last_saldo',
    ];
}
