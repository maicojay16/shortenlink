<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{

    public function linkAnalytic()
    {
        return $this->hasMany('App\LinkAnalytic', 'link_id', 'id');
    }
}
