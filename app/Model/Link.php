<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{

    public function linkAnalytic()
    {
        return $this->hasMany('App\Model\LinkAnalytic', 'link_id', 'id');
    }
}
