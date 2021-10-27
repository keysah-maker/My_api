<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class user extends Authenticatable
{
    use Notifiable, HasApiTokens, HasFactory;
    
    public $timestamps = false;

    protected $fillable = [
        "id", "username", "pseudo", "created_at", "email", "password", "updated_at", "token"
    ];

    protected $table = "user";
}
