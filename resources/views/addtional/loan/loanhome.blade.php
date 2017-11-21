@extends('template.app')

{{-- set title --}}
@section('title', 'Manage Loan')

{{-- set main content --}}
@section('content')

<body class="hold-transition skin-blue sidebar-mini">
  <div class="wrapper">
    <header class="main-header">
    <!-- Logo -->
    <a href="{{ url('dashboard/home') }}" class="logo">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <span class="logo-mini"><b>MG</b>S</span>
      <!-- logo for regular state and mobile devices -->
      <span class="logo-lg"><b>Management</b> System</span>
    </a>

     <nav class="navbar navbar-static-top">
    <!-- Sidebar toggle button-->
    <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
      <span class="sr-only">Toggle navigation</span>
    </a>
          @include('template.menu')
      </nav>
    </header>

         @include('template.sidebar')

    <div class="content-wrapper">
      <!-- Main content -->
      <section class="content-header">
        <h1>
          Loan
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Loan Home</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">

            {{-- alert --}}
            @if (session('status'))
              <div class="alert alert-success">
            {{ session('status') }}
              </div>
            @endif
            @if (session('error'))
              <div class="alert alert-danger">
            {{ session('error') }}
              </div>
            @endif

            {{-- search --}}
            <!-- Search FIX -->
              <form class="form" action="{{ url('/addtional/loan_home/getrange') }}" method="post">
                {{csrf_field()}}
                <div class="box">
                  <div class="box-body">
                    <div class="col-md-6">
                      <div class="col-md-6">
                        <div class="row">
                          <div class='date' id='datetimepicker1'>
                            <label class="control-label">Start Date</label>
                            <input type="text" id="dateStart" name="dateStart" class="form-control for_date" @if(isset($arraydata[0])) value="{{$arraydata[0]}}" @endif>
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="row">
                          <div class='date' id='datetimepicker1'>
                            <label class="control-label">End Date</label>
                            <input type="text" id="dateEnd" name="dateEnd" class="form-control for_date" @if(isset($arraydata[1])) value="{{$arraydata[1]}}" @endif>
                          </div>
                        </div>
                      </div>

                      <label class="control-label">Loan ID</label>
                      <input class="form-control" type="text" id="idloan" name="idloan" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                    </div>

                    <div class="col-md-6">
                        <div class="col-md-4">
                          <div class="row">
                            <label class="control-label">Type User</label>
                            <select class="form-control" name="typeuser">
                              <option value="employee" @if(isset($arraydata[3])) @if($arraydata[3]=="employee") selected="true"  @endif @endif>Employee</option>
                              <option value="customer" @if(isset($arraydata[3])) @if($arraydata[3]=="customer") selected="true"  @endif @endif>Customer</option>
                              <option value="supplier" @if(isset($arraydata[3])) @if($arraydata[3]=="supplier") selected="true"  @endif @endif>Supplier</option>
                              <option value="addtional" @if(isset($arraydata[3])) @if($arraydata[3]=="addtional") selected="true"  @endif @endif>Addtional</option>
                            </select>
                          </div>
                        </div>
                        <div class="col-md-8">
                          <div class="row">
                            <label class="control-label">User</label>
                            <input class="form-control" type="text" id="iduser" name="iduser" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                          </div>
                        </div>
                    </div>
                  </div>
                  <div class="box-footer">
                    <div class="pull-right">
                      <a style="width : 90px;" href="{{url('addtional/loan_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                      <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            <!-- Search FIX -->
            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Loan</h3>
                </div>

                <div class="box-body table-responsive">

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/addtional/loan_home/add') }}" type="button" class="btn btn-primary">Add New Loan</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>Loan ID</th>
                        <th>Loan user</th>
                        <th>Grand Total</th>
                        <th>Loan Already Paid</th>
                        <th>Total Loan payable</th>
                        <th>Status Loan</th>
                        <th>Due Date</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>
                        @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                          <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($lt_loan as $item)
                      <?php $last_total =  $item->total - $item->bayar;?>
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>@if($item->loan_type != 1){{ $item->loan_id .' - '. $item->user_id  }}@else {{ $item->loan_id }} @endif</td>
                        <td>@if(substr($item->user_id,0,1) == 'E'){{ $item->employee_name .' - '. $item->user_id }}
                        @elseif(substr($item->user_id,0,1) == 'C'){{ $item->customer_name .' - '. $item->user_id }}
                        @elseif(substr($item->user_id,0,1) == 'S'){{ $item->supplier_name .' - '. $item->user_id }}
                        @elseif(substr($item->user_id,0,1) == 'A'){{ $item->user_id }}@endif</td>
                        <td>{{number_format($item->total) }}</td>
                        <td>{{number_format($item->bayar) }}</td>
                        <td>{{number_format($last_total) }}</td>
                        <td>@if($item->status_loan == 1){{ 'UNPAID' }}@else{{ 'PAID' }}@endif</td>
                        <td><?= date("d/m/Y",strtotime($item->tanggal_jatuh_tempo));?></td>
                        <td>{{ $item->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                        <td>@if($item->status == 'A') {{ 'Active' }} @elseif($item->status == 'D') {{ 'Deactive' }} @elseif($item->status == 'T') {{ 'Temporary' }} @endif</td>

                @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <td><a style="width:90px;" href="{{ url('/addtional/loan_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        @endif

                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>Loan ID</th>
                        <th>Load user</th>
                        <th>Grand Total</th>
                        <th>Loan Already Paid</th>
                        <th>Total Loan payable</th>
                        <th>Status Loan</th>
                        <th>Due Date</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>

                        @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                          <th>Action</th>
                        @endif
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="box-footer">
                  <form class="form" action="{{ url('/addtional/loan_home/export') }}" method="post">
                    {{ csrf_field() }}
                    <input type="hidden" name="dateStart" value="<?php if(isset($arraydata[0])){ echo $arraydata[0]; }?>">
                    <input type="hidden" name="dateEnd" value="<?php if(isset($arraydata[1])){ echo $arraydata[1]; }?>">
                    <input class="form-control" type="hidden" id="idloan" name="idloan" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                    <input class="form-control" type="hidden" id="iduser" name="iduser" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                    <input class="form-control" type="hidden" id="typeuser" name="typeuser" pattern=".{3,}" title="3 characters minimum"  @if(isset($arraydata[3])) @if($arraydata[3]=="employee") value="employee"  @elseif($arraydata[3]=="customer") value="customer" @elseif($arraydata[3]=="supplier") value="supplier" @elseif($arraydata[3]=="addtional") value="addtional" @endif @endif>
                    <button style="width:90px;" type="submit" class="btn btn-success pull-right">Export</button>
                  </form>
                </div>

              </div>
            </div>
          </div>
      </section>

    </div>
  </div>

  <script>


      $(function () {
        $('#table-home tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
        });

        var table = $('#table-home').DataTable({
          responsive: true,
          stateSave: true,
          "paging": true,
          "lengthChange": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "order": [[ 0, "desc" ]],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'Data Loan',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'Data Loan',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'Data Loan',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'Data Loan',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed four-column',
                columnText: function ( dt, idx, title ) {
                    return (idx+1)+': '+title;
                }
              },
          ],
            columnDefs: [ {
                targets: -1,
                visible: false
            }]
        });
        // Apply the search
        table.columns().every( function () {
          var that = this;
          $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          });
        });

        // for datetimepicker
        $('.for_date').datetimepicker({
          format: 'YYYY/MM/DD'
        });

      });
    </script>
<body>
@endsection
