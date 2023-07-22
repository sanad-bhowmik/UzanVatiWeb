<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignJoinRequest extends Model
{
    //
    protected $fillable = ['campaign_id','vendor_id','admin_note','vendor_note','remarks','status','flag'];
}
