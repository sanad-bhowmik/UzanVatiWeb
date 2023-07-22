<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    //
    protected $fillable = ['title','name','code','banner','vendor_note','file','start_date','end_date','start_time','end_time','status',];


    public function campaignName(){
       
        return $this->name;
    }

}
