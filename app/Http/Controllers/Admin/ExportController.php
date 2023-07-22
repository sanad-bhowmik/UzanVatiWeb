<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Models\Order;
use App\Models\VendorOrder;
use Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromArray;


class ExportController implements FromArray
{
    // use Exportable;

    // public function collection()
    // {   
    //     $user = Auth::user();
    //     $orders = VendorOrder::where('user_id','=',37)->orderBy('id','desc')->get();

    //     $order_details[] = array('order_number','status');
    //     //dd($orders);
    //     foreach($orders as $order){
    //        // dd($order[0]['order_number']);
            
    //         $order_details[] = array(
    //             'order_number' => $order[0]['order_number'],
    //             'status' => $order[0]['status']
    //        );
        
    //     }
    //     dd($orders);
    //     return VendorOrder::all();
    // }


    protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices;
    }




}