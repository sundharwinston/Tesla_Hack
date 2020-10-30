<?php

namespace App\Http\Controllers\AdminControllers;

use App\Owner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class laundryshopController extends Controller
{

    public function index(){
        $Data['Owner_details']=Owner::get();
        return view('admin.LaundryShop.view',$Data);
    }
    public function addLaundryShop(){
        return view('admin.LaundryShop.addshop');
    }

    public function SaveOwner(Request $request)
    {
        $this->validate(request(), [
            'shop_name' => 'required',
            'name' => 'required',
            'address' => 'required',
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'email' => 'required|unique:owners',
            'password' => 'required|confirmed'
        ]);

        try {
            $Owner = new Owner;
            $Owner->admin_id = auth()->user()->id;
            $Owner->shop_name = request('shop_name');
            $Owner->owner_name = request('name');
            $Owner->address = request('address');
            $Owner->latitude = request('lat');
            $Owner->longitude = request('lan');
            $Owner->mobile = request('mobile');
            $Owner->email = request('email');
            $Owner->password = bcrypt(request('password'));
            $Owner->save();
            return redirect(route('admin.ViewOwner'))->with('success', 'Owner Added Successfully!');
        } catch (Exception $e) {
            return back()->with('danger', 'Something went wrong!');
        }
    }
    public function editOwner($id){
        $Data['edit_owner'] = Owner::find($id);
        return view('admin.LaundryShop.edit',$Data);
    }
    public function UpdateOwner($id){
        $this->validate(request(), [
            'shop_name' => 'required',
            'name' => 'required',
            'address' => 'required',
            'mobile' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'email' => 'required',
            'password' => 'required|confirmed'
        ]);
        try {
            $Edit_Owner = Owner::findOrFail($id);
            $Edit_Owner->admin_id = auth()->user()->id;
            $Edit_Owner->shop_name = request('shop_name');
            $Edit_Owner->owner_name = request('name');
            $Edit_Owner->address = request('address');
            $Edit_Owner->latitude = request('lat');
            $Edit_Owner->longitude = request('lan');
            $Edit_Owner->mobile = request('mobile');
            $Edit_Owner->email = request('email');
            $Edit_Owner->password = bcrypt(request('password'));
            $Edit_Owner->save();
            return redirect(route('admin.ViewOwner'))->with('success', 'Owner Updated Successfully!');
        } catch (Exception $e) {
            return back()->with('danger', 'Something went wrong!');
        }
    }
    public function deleteOwner($id){
       try{
           Owner::find($id)->delete();
           return back()->with('success','Owner deleted successfully');

       }catch (Exception $e){
           return back()->with('danger', 'Something went wrong!');

       }
    }

}
