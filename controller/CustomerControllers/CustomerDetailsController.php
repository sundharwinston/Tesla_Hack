<?php

namespace App\Http\Controllers\CustomerControllers;

use App\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CustomerDetailsController extends Controller
{
    public function ViewProfile(){
        $Data['Customer_Details']=Customer::where([['id',auth()->user()->id]])->get()->first();
        return view('customer.profile',$Data);
    }
   public function customerLocation(Request $request){
        $Customer_Location = Customer::findOrFail(request('id'));
        $Customer_Location->latitude=request('latidude');
        $Customer_Location->longitude=request('longitude');
        $Customer_Location->save();
       return "success";
   }
}
