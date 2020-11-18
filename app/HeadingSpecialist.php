<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HeadingSpecialist extends Model
{
    //


    public function specialists()
    {
        return $this->hasMany(Specialist::class);
    }
}
