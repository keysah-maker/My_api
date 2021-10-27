<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class file extends Model
{
    
    public $timestamps = false;

    protected $fillable = [
        "id", "name", "source", "user_id", "created_at", "updated_at"
    ];

    protected $table = "file";
}
