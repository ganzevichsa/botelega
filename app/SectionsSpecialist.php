<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SectionsSpecialist extends Model
{
    //


    public function specialists()
    {
        return $this->hasMany(Specialist::class);
    }
}
