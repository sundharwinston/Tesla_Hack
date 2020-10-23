@extends('layout.master')

@section('content')




<!DOCTYPE html>
<html>
   <head>
      <title>User Details</title>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
      <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
   </head>
   <body>
      <div class="container">
         <form class="form-horizontal" action="{{ action('UserController@update',$User->id) }}" method="post" enctype="multipart/form-data">
            <input type="hidden" name="_method" value="PUT">
            {{ csrf_field() }}
           
            <h3 class="text-center"><b>User Profile Details</b></h3>
             <div class="row">
               <div class="col-sm-4">
                  <div class="form-group">
                     <label for="contact-me-name" class="form-label form-label-outside">User Name</label>
                     <input id="admission-me-name" type="text" name="name" value="{{$User->name}}" data-constraints="@Required" class="form-control"required>
                  </div>
               </div>
               <div class="col-sm-4">
                  <div class="form-group">
                     <label for="admission-me-father_name" class="form-label form-label-outside"><b>Email</b></label>
                     <input id="admission-me-father_name" type="text" name="email" value="{{$User->email}}" data-constraints="@Required" class="form-control">
                  </div>
               </div>


               <div class="col-sm-4">
                  <div class="form-group">
                     <p>Active/InActive</p>
                    <input type="radio" id="male" name="status" value="1" {{ $User->status == '1' ? 'checked' : ''}}>
                    <label for="male">Active</label><br>
                    <input type="radio" id="female" name="status" value="0" {{ $User->status == '0' ? 'checked' : ''}}>
                    <label for="female">In-Active</label><br>
               </div>


            </div>
            </div>

             <div class="card-footer text-right">
                  <button type="submit" class="btn btn-primary pull-left">Save Changes</button>
             </div>
         </form>
      </div>
   </body>
   <!-- Global site tag (gtag.js) - Google Analytics -->
  
   <style>
      .div-wrapper {
      height: 200px;
      margin-top: 40px;
      border: 2px dashed #ddd;
      border-radius: 8px;
      }
      .div-to-align {
      width: 75%;
      padding: 40px 20px;
      /* .... */
      }
      /* The container */
      .box{
      color: black;
      padding: 20px;
      display: none;
      margin-top: 20px;
      }
      .online{ background: #F0F8FF
      ; }
   </style>
</html>
@endsection