<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Controllers\UserController;

class token extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "id", "code", "expired_at", "user_id"
    ];

    protected $table = "token";
}
