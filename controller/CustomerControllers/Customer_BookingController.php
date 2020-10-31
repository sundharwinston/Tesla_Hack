<?php

namespace App\Http\Controllers\CustomerControllers;

use App\LaundryBooking;
use App\Owner;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class Customer_BookingController extends Controller
{
    public function Booking($owner_id){
        try {
            $Data['Shop_Details']=Owner::findorfail($owner_id);
            return view('customer.Booking.Add_Booking',$Data);
        } catch (\Exception $e) {
            return back()->with('danger', 'Something went wrong!');
        }
    }
    

    public function cloths_type(Request $request){
        $Cloth_Types = [];
        if($request->has('q')){
            $Cloth_Types = Product::where('cloth_type','LIKE','%'.$request->has('q').'%')->get();
        }
        return response()->json($Cloth_Types);
    }



    public function SaveBooking(Request $request){
        $this->validate(request(), [
            'customer_name' => 'required',
            'cloth_types' => 'required',
            'customer_mobile' =>'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10'
        ]);
        try {
            foreach (request('cloth_types') as $Cloth_Type){
                $Booking = new LaundryBooking;
                $Booking->owner_id = request('owner_id');
                $Booking->customer_id = auth()->user()->id;
                $Booking->cloth_types = $Cloth_Type;
                $Booking->save();
            }
            return back()->with('success', 'Booking Added Successfully!');
        } catch (\Exception $e) {
            return back()->with('danger', 'Something went wrong!');
        }

    }
}
