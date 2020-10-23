@extends('layouts.app')

@section('content')
<div class="container">
     <!-- csrf_token()  -->
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="container">
                <!-- {{$users}} -->
              <h2>User Data</h2>
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th>Image</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Gender</th>
                    <th>address</th>
                    <th>city</th>
                    <th>state</th>
                    <th>Hobbies</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td><img src="images/{{ $user->image }}" width="50px"></td>
                            <td>{{$user->name}}</td>
                            <td>{{$user->email}}</td>
                            <td>{{$user->mobile}}</td>
                            <td>{{$user->gender}}</td>
                            <td>{{$user->address}}</td>
                            <td>{{$user->city}}</td>
                            <td>{{$user->state}}</td>
                                <td>{{implode(',',unserialize($user->hobbies))}}</td>
                           <td>
                                <form action="{{ action('MemberController@delete',$user->id) }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="_method" value="DELETE">
                                    <a href="{{ action('MemberController@edit',$user->id) }}" class="btn">Edit</i></a>
                                    <button  onclick="return confirm('Are you sure?')" class="btn">delete</button>
                                </form>
                            </td>
                          </tr>
                    @endforeach
                  
                  
                </tbody>
              </table>
            </div>
        </div>
    </div>
</div>
@endsection
