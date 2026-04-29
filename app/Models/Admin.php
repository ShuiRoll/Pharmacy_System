<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Admin extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'adminID';

    protected $fillable = ['userID', 'specialization', 'department'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
}