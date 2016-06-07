<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Scrap extends Model
{
    protected $fillable=['name','title','subtitle','date','img','link'];
    protected $table='scrap';
}
