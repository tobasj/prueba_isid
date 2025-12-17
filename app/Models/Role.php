<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    protected $table = 'roles';
    protected $fillable = ['name','slug'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
