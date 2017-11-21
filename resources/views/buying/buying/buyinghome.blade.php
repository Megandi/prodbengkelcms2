@extends('template.app')

{{-- set title --}}
@section('title', 'Manage Buying')

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
          Buying
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Buying Home</li>
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
              <form class="form" action="{{ url('/buying/buying_home/getrange') }}" method="post">
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

                      <label class="control-label">Nota</label>
                      <input class="form-control" type="text" id="nota" name="nota" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                    </div>

                    <div class="col-md-6">
                      <label class="control-label">ID Buying</label>
                      <input class="form-control" type="text" id="idadd" name="idadd" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>

                      <label class="control-label">Supplier</label>
                      <input class="form-control" type="text" id="supp_id" name="supp_id" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                    </div>

                  </div>
                  <div class="box-footer">
                    <div class="pull-right">
                      <a style="width : 90px;" href="{{url('buying/buying_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                      <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            <!-- Search FIX -->

            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Buying</h3>
                </div>

                <div class="box-body table-responsive">

                @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/buying/buying_home/add') }}" type="button" class="btn btn-primary">Add New Buying</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Buying</th>
                        <th>No Nota</th>
                        <th>Date Buy</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Supplier</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($buyings as $buying)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($buying->created_date));?></td>
                        <td><a style="width:90px;" href="{{url('/buying/buying_home/detail/'.$buying->id)}}">{{ $buying->pembelian_id }}</a></td>
                        <td>{{ $buying->no_nota }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($buying->tanggal));?></td>
                        <td>{{ number_format($buying->pembelian_total)}}</td>
                        @if($buying->status_hutang)
                          <td>@if($buying->status_hutang==1) UNPAID @else PAID @endif</td>
                        @else
                          <td>PAID</td>
                        @endif
                        @if($buying->no_nota =="")
                          <td></td>
                        @else
                          <td>{{ $buying->suppid }} - {{ $buying->namasupplier }} </td>
                        @endif
                        <td>{{ $buying->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($buying->last_modify_date));?></td>
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                          @if($buying->no_nota=="")
                            <td><a style="width:90px;" href="{{ url('/buying/buying_home/addnext/'.$buying->id) }}" type="button" class="btn btn-warning">Continue</a></td>
                          @else
                            <td><a style="width:90px;" href="{{ url('/buying/buying_home/edit/'.$buying->id) }}" type="button" class="btn btn-info">Edit</a></td>
                          @endif
                        @endif
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                          @if($buying->no_nota=="")
                            @if(Auth::user()->karyawan_id==$buying->modify_user_id)
                              <td><a style="width:90px;" href="{{ url('/buying/buying_home/delete/'.$buying->id) }}" type="button" onclick="return confirm('Are you sure you want to delete this data ?')" class="btn btn-danger">Delete</a></td>
                            @else
                              <td><a style="width:90px;" href="" type="button" class="btn btn-danger disabled">Delete</a></td>
                            @endif
                          @else
                            <td><a style="width:90px;" href="{{ url('/buying/buying_home/delete/'.$buying->id) }}" onclick="return confirm('Are you sure you want to delete this data ?')" type="button" class="btn btn-danger">Delete</a></td>
                          @endif
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Buying</th>
                        <th>No Nota</th>
                        <th>Date Buy</th>
                        <th>Total Price</th>
                        <th>Status</th>
                        <th>Supplier</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </tfoot>
                  </table>
                  </div>

                  <div class="box-footer">
                    <form class="form" action="{{ url('/buying/buying_home/export') }}" method="post">
                      {{ csrf_field() }}
                      <input type="hidden" name="dateStart" value="<?php if(isset($arraydata[0])){ echo $arraydata[0]; }?>">
                      <input type="hidden" name="dateEnd" value="<?php if(isset($arraydata[1])){ echo $arraydata[1]; }?>">
                      <input class="form-control" type="hidden" id="nota" name="nota" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                      <input class="form-control" type="hidden" id="idadd" name="idadd" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                      <input type="hidden" id="supp_id" name="supp_id" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
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
                  title: 'TUNAS ABADI 8 | Data Buying',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Buying',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Buying',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Buying',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed three-column',
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
