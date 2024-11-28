<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class R_types extends Model
{
    use HasFactory;
    protected $table = 'r_types';
    protected $fillable = [
        'type'
    ];
    public function request()
    {
        return $this->hasMany(Request::class,'r_type_id');
    }
}
