<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CycleCount extends Model
{
    protected $primaryKey = 'countID';

    protected $casts = [
        'count_date' => 'date',
    ];

    protected $fillable = [
        'userID',
        'count_date',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userID', 'id');
    }

    public function cycleCountLines()
    {
        return $this->hasMany(CycleCountLine::class, 'countID', 'countID');
    }
}