@extends('template.app')

{{-- set title --}}
@section('title', 'Sales Return')

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
          Return
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Sales Return Home</li>
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
              <form class="form" action="{{ url('/sales/salreturn_home/getrange') }}" method="post">
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

                      <label class="control-label">ID Purchase Return</label>
                      <input class="form-control" type="text" id="idreturn" name="idreturn" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                    </div>

                    <div class="col-md-6">
                      <label class="control-label">ID Selling</label>
                      <input class="form-control" type="text" id="idselling" name="idselling" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>

                      <label class="control-label">Total Price Return</label>
                      <input class="form-control" type="number" id="total" name="total" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                    </div>

                  </div>
                  <div class="box-footer">
                    <div class="pull-right">
                      <a style="width : 90px;" href="{{url('sales/salreturn_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                      <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            <!-- Search FIX -->
            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Sales Return</h3>
                </div>

                <div class="box-body table-responsive">

                @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/sales/salreturn_home/add') }}" type="button" class="btn btn-primary">Created New Return Items</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Sales Return</th>
                        <th>ID Selling</th>
                        <th>Quantities Total Return</th>
                        <th>Total Price Return</th>
                        {{--  @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif --}}
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($tr_returpenjualan as $item)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>{{$item->retur_id}}</td>
                        <td>{{$item->b_penjualan_id}}</td>
                        <td>{{$item->jumlahqty}}</td>
                        <td>{{number_format($item->total_return)}}</td>
                        {{--<td>@if($item->type_return == 1){{'Cut the debt'}}@elseif($item->type_return == 2){{'Change Money'}}@elseif($item->type_return == 3){{'Replace items at the same price'}}@elseif($item->type_return == 4){{'Trade-in Goods'}}@endif</td>--}}
                        {{--@if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <td><a style="width:90px;" href="{{ url('/sales/saleturn_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        @endif--}}
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->d==1)
                        {{--<td><a style="width:90px;" href="{{ url('/sales/salreturn_home/delete/'.$item->id.'/'.$item->stock_id.'/'.$item->total_qty_return.'/'.$item->stock_sell) }}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>--}}
                        <td><a style="width:90px;" href="{{ url('/sales/salreturn_home/addnext/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        <td><a style="width:90px;" href="{{ url('/sales/salreturn_home/delete/'.$item->id) }}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Sales Return</th>
                        <th>ID Selling</th>
                        <th>Quantities Total Return</th>
                        <th>Total Price Return</th>
                        {{--  @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif --}}
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        <th>Action</th>
                        @endif
                      </tr>
                    </tfoot>
                  </table>
                  </div>

                  <div class="box-footer">
                    <form class="form" action="{{ url('/sales/salreturn_home/export') }}" method="post">
                      {{ csrf_field() }}
                      <input type="hidden" name="dateStart" @if(isset($arraydata[0])) value="{{$arraydata[0]}}" @endif>
                      <input type="hidden" name="dateEnd" @if(isset($arraydata[1])) value="{{$arraydata[1]}}" @endif>
                      <input type="hidden" id="idreturn" name="idreturn" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
                      <input type="hidden" id="idselling" name="idselling" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                      <input type="hidden" id="total" name="total" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                      <button style="width:90px;" type="submit" class="btn btn-success pull-right">Export</button>
                    </form>
                  </div>

                {{-- footer --}}

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
          // dom: 'lrtipB',
          // buttons: [
          //         'copy', 'csv', 'excel', 'pdf', 'print'
          // ]
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'TUNAS ABADI 8 | Data Sales Return',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Sales Return',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Sales Return',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Sales Return',
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
