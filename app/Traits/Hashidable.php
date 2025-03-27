<?php

namespace App\Traits;

use Vinkla\Hashids\Facades\Hashids;

trait Hashidable
{
    public function getHashedIdAttribute()
    {
        return Hashids::encode($this->id);
    }
}
