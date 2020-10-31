@extends('layouts.master')

@section('HomeMenu','active')

@section('BreadCrumb')
    <section class="content-header">
       <h4>Dashboard</h4>
        <ol class="breadcrumb">
            <li class="active"><a href="{{ url('home') }}"><i class="fa fa-dashboard"></i>Dashboard</a></li>
        </ol>
    </section>
@endsection

@section('content')

    <div class="row">
         <div class="col-sm-12">
            @component('layouts.component.box-pannel',['title'=>'Home','color'=>env('TABPANELCOLOR')])

             @endcomponent
        </div>
    </div>
@endsection

