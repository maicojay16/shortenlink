<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LinkAnalytic extends Model
{

    public function link()
    {
        return $this->hasOne('App\Link', 'id', 'link_id');
    }
}
