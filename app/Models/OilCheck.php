<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OilCheck extends Model
{
    protected $fillable = [
        'odometer',
        'date'
    ];

    protected $casts = [
        'odometer' => 'integer',
        'date' => 'date',
        ];


    public function vehicle():belongsTo {
        return $this->belongsTo(Vehicle::class);
    }
}
