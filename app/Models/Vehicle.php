<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    protected $fillable = [
        'brand',
        'model',
        'year',
        'mileage',
    ];

    protected $casts = [
        'year' => 'integer',
        'mileage' => 'integer',
    ];


    public function oilChanges():HasMany {

        return $this->hasMany(OilCheck::class);
    }



    public function getMetrics() {
        $lastChange = $this->getLastChange();

        if($lastChange) {
            $kmSince = $this->mileage - $lastChange->odometer;
            $daySince = $lastChange->date->diffInDays(now());
        }else{
            $daySince = $this->created_at->diffInDays(now());
            $kmSince = $this->mileage;
        }



        return [
            'kmSince' => $kmSince,
            'daySince' => $daySince,
        ];
    }


    public function isDue() {

        $metrics = $this->getMetrics();

        $kmSince = $metrics['kmSince'];
        $daySince = $metrics['daySince'];

        $isDue = ($kmSince >= 5000) || ($daySince >= 180);

        return $isDue;
    }



    public function getLastChange() {
        $lastChange = $this->oilChanges()->latest('date')->first();


        return $lastChange;
    }

}
