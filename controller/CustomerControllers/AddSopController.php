<?php

namespace App\Http\Controllers\CustomerControllers;

use App\Customer;
use App\Owner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AddSopController extends Controller
{
    public function addShop(){
        $Data['Shop_Details']=Owner::get();
        $Data['Near_Shops']= Owner::select(DB::raw('*, ( 6367 * acos( cos( radians(11.2194391) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(78.16772360000004) ) + sin( radians(11.2194391) ) * sin( radians( latitude ) ) ) ) AS distance'))->having('distance', '<', 30)->get();
        $latitudeFrom=11.2194391;
        $longitudeFrom=78.16772360000004;
        $latitudeTo=13.0445224;
        $longitudeTo=80.19367280000006;
        $earthRadius = "K";
        $Data['DistanceBetweenPLaces']=$this->getDistance($latitudeFrom,$longitudeFrom,$latitudeTo,$longitudeTo,$earthRadius);
        return view('customer.AddShop.index',$Data);
    }
    function getDistance($lat1, $lon1, $lat2, $lon2,$unit){
       $theta = $lon1 - $lon2;
          $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
          $dist = acos($dist);
          $dist = rad2deg($dist);
          $miles = $dist * 60 * 1.1515;
          $unit = strtoupper($unit);
          if ($unit == "K") {
              return ($miles * 1.609344);
          } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }
    public function ViewShop($id){
        return Owner::where([['id',$id]])->get()->first();
    }

    public function ShopWay($owner_id){
        $Data['Customer']=Customer::where([['id',auth()->user()->id]])->get()->first();
        $Data['Selected_Shop']=Owner::where([['id',$owner_id]])->get()->first();
        return view('customer.ShopWay.shop_way',$Data);
    }

    public function Booking($owner_id){
        $Data['Shop_Details']=Owner::where([['id',$owner_id]])->get()->first();
        return view('customer.Booking.Add_Booking',$Data);
    }


}
