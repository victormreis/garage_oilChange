<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Owner extends Model
{
    protected $fillable = [
      "name",
      "email",
      "phone",
    ];


    public function vehicle() {
     return $this->hasOne(Vehicle::class);
    }
}
