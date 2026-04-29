<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'staffID';

    protected $casts = [
        'hire_date' => 'date',
    ];

    protected $fillable = ['userID', 'position', 'hire_date', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }
}