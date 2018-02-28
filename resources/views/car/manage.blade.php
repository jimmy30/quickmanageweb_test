@extends('layouts.app')  
  <!-- Content Wrapper. Contains page content -->
  
  @section('content')
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Cars
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Cars</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <p><a href="{{ route('add-car') }}" class="btn btn-primary">Add New</a></p>
        <div class="box box-primary">
            
            @if(Session::has('success'))
                  <div class="alert alert-success alert-dismissable">
                      <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                      {{ Session::get('success') }}
                  </div>
              @endif

              @if(Session::has('fail'))
                  <div class="alert alert-danger alert-dismissable">
                      <button class="close" aria-hidden="true" data-dismiss="alert" type="button">×</button>
                      {{ Session::get('fail') }}
                  </div>
              @endif
              
            <table id="datatable" class="table table-bordered">
            <thead>
                <tr>
                  <th>ID</th>
                  <th>Car Name</th>
                  <th>Action</th>
                </tr>
            </thead>
            </table>
        </div>
    </section>
    <!-- /.content -->
  </div>
  
  
  @endsection
  
  @section('script')
  <script>
      var table;
        $(document).ready(function() {
            table = $('#datatable').DataTable( {
		"bProcessing": true,
		"bServerSide": true,
                "sScrollX": true,
//                "sScrollY": "300px",
                "autoWidth": false,
                "iDisplayLength":50,
                "sAjaxSource": "{{ route('get-ajax-cars') }}",
                "fnServerData": function ( sSource, aoData, fnCallback, oSettings ) {
                    oSettings.jqXHR = $.ajax( {
                      "dataType": 'json',
                      "type": "GET",
                      "url": sSource,
                      "data": aoData,
                      "success": fnCallback,
                      "error": handleAjaxError // this sets up jQuery to give me errors
                    } );
                  },

                "columns": [
                    { "data": 0, "defaultContent": ""},
                    { "data": 1, "defaultContent": ""},
                    { "data": 2, "orderable": false },
                ],
                "order": [[ 0, "desc" ]],
                "initComplete" : function () {
                }
            })
        });
        
    function handleAjaxError( xhr, textStatus, error ) {
        //table.fnProcessingIndicator( false );
    }
      </script>
      
@endsection