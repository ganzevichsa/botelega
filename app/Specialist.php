<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specialist extends Model
{
    //
    public function country(){

        return $this->belongsTo(Country::class);
    }

    public function city(){

        return $this->belongsTo(City::class);
    }
    public function heading(){

        return $this->belongsTo(HeadingSpecialist::class);
    }
    public function section(){

        return $this->belongsTo(SectionsSpecialist::class);
    }
}
