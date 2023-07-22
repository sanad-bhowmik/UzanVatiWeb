<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayHistory extends Model
{
    //
   // protected $table = 'order_pay_histories';

    protected $fillable = ['order_id','statusCode','statusMessage','paymentID','payerReference',
    'paymentExecuteTime','order_number','pay_amount','txn_id',
    'method','paid_by','remarks','response','currency'];
    


    public function details(){
        
        return $this->belongsTo('App\Models\Order','id','order_id');
    }

}
