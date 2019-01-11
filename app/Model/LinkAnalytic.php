<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LinkAnalytic extends Model
{

    public function link()
    {
        return $this->hasOne('App\Model\Link', 'id', 'link_id');
    }
}
