
@extends('layout.master')

@section('content')

<link href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">

                <div class="table-responsive">
                    <table class="table table-bordered" id="UserTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                       
                    </table>
                </div>
        </div>
    </div>

    <!-- <script async src="https://www.googletagmanager.com/gtag/js?id=UA-143387476-1"></script> -->

    <script src="//code.jquery.com/jquery.js"></script>
        <!-- DataTables -->
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
        <!-- Bootstrap JavaScript -->
        <script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <script>
        var Users= $('#UserTable').DataTable({
            processing: true,
            serverSide: true,
            Filter: true,
            ajax: '{{ action('UserController@index') }}',
            "columns": [
                {data: 'name', name: 'name'},
                {data: 'action', name: 'action'}
            ],
          
        });


        $('#UserTable').on('click', '.Delete', function (e) { 
            e.preventDefault();
            var url = $(this).attr('href');
            var DeleteMessage = $(this).attr('DeleteMessage');
            swal({
                title: "Are you sure?",
                text: DeleteMessage,
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: false
            },function(isConfirm) {
                if (isConfirm) {
                    $.ajax(
                        {headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: url,
                        type: 'DELETE',
                        dataType: 'json',
                    }).always(function (data) {
                        Users.ajax.reload();
                        swal("Deleted!", data.msg, data.status);
                    });
                } else {
                    swal("Cancelled", "Your Data is safe", "error");
                }
            });
        });
    </script>
@endsection