@extends('admin.layouts.master')

@section('Current-Page')
View Deposit
@endsection

@section('Parent-Menu')
Transactions
@endsection

@section('Menu')
View Deposit
@endsection

@section('content')
    <div class="tile">
      <div class="pad">
        <div class="row section-gap">
          <div class="col-lg-10">

          </div>
          <div class="col-lg-2">
            <button class="btn btn-primary" type="button" onclick="window.location.href='add_deposit.php'">Add Deposit</button>
          </div>
        </div><br>
        <div class="row ">
          <div class="col-lg-2"></div>
          <div class="col-lg-10 col-xs-12">
              <div class="tile">
                <table class="table">
                  <thead>
                    <tr>
                      <th>S.No</th>
                      <th>Date</th>
                      <th>Ledger</th>
                      <th>Amount</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>1</td>
                      <td>12.01.2019</td>
                      <td>Name</td>
                      <td>$121</td>
                    </tr>

                  </tbody>
                </table>
              </div>
            </div>
        </div>
      </div>
    </div>
 @endsection