@extends('template.app')

{{-- set title --}}
@section('title', 'Addtional Cost List')

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
          Addtional Cost
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Addtional Cost Home</li>
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

            <!-- Search FIX -->
              <form class="form" action="{{ url('/finance/addtional_home/getrange') }}" method="post">
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
                      <label class="control-label">ID Addtional</label>
                      <input class="form-control" type="text" id="idadd" name="idadd" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>

                      <label class="control-label">Amount Cost</label>
                      <input class="form-control" type="number" id="amount" name="amount" pattern=".{3,}" title="3 characters minimum" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                    </div>

                  </div>
                  <div class="box-footer">
                    <div class="pull-right">
                      <a style="width : 90px;" href="{{url('finance/addtional_home')}}" type="button" onclick="return confirm('Are you sure want to reset?')" class="btn btn-warning">Reset</a>
                      <button style="width : 90px;" type="submit" class="btn btn-primary">Submit</button>
                    </div>
                  </div>
                </div>
              </form>
            <!-- Search FIX -->

            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Addtional Cost</h3>
                </div>

                <div class="box-body table-responsive">

                @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/finance/addtional_home/add') }}" type="button" class="btn btn-primary">Add New Addtional Cost</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Addtional</th>
                        <th>Nota</th>
                        <th>Date</th>
                        <th>Total QTY</th>
                        <th>Amount Cost</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($lt_biayalain as $item)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>{{$item->biayalain_id}}</td>
                        <td>{{$item->no_nota}}</td>
                        <td>{{date("d/m/Y",strtotime($item->tanggal))}}</td>
                        <td>{{$item->totaljumlah_detail}}</td>
                        <td>{{ number_format($item->total_biaya) }}</td>
                        <td>{{ $item->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                        <td>@if($item->status == 'A') {{ 'Active' }} @elseif($item->status == 'D') {{ 'Deactive' }} @elseif($item->status == 'T') {{ 'Temporary' }} @endif</td>
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                          @if($item->no_nota=="")
                            <td><a style="width:90px;" href="{{ url('/finance/addtional_home/addnext/'.$item->id) }}" type="button" class="btn btn-warning">Continue</a></td>
                          @else
                            <td><a style="width:90px;" href="{{ url('/finance/addtional_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                          @endif
                        @endif
                        @if(DB::table('level_akses')->where('id_menu', '3')->where('id_level',auth::user()->level_id)->first()->u==1)
                          @if($item->no_nota=="")
                            @if(Auth::user()->karyawan_id==$item->modify_user_id)
                              <td><a style="width:90px;" href="{{ url('/finance/addtional_home/delete/'.$item->id) }}" type="button" class="btn btn-danger">Delete</a></td>
                            @else
                              <td><a style="width:90px;" href="" type="button" class="btn btn-danger disabled">Delete</a></td>
                            @endif
                          @else
                            <td><a style="width:90px;" href="{{ url('/finance/addtional_home/delete/'.$item->id) }}" type="button" class="btn btn-danger">Delete</a></td>
                          @endif
                        @endif

                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>ID Addtional</th>
                        <th>Nota</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Amount Cost</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>
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
                    <form class="form" action="{{ url('/finance/addtional_home/exportt') }}" method="post">
                      {{ csrf_field() }}
                      <input type="hidden" name="dateStart" @if(isset($arraydata[0])) value="{{$arraydata[0]}}" @endif>
                      <input type="hidden" name="dateEnd" @if(isset($arraydata[1])) value="{{$arraydata[1]}}" @endif>
                      <input type="hidden" id="nota" name="nota" @if(isset($arraydata[3])) value="{{$arraydata[3]}}" @endif>
                      <input type="hidden" id="amount" name="amount" @if(isset($arraydata[4])) value="{{$arraydata[4]}}" @endif>
                      <input type="hidden" id="idadd" name="idadd" @if(isset($arraydata[2])) value="{{$arraydata[2]}}" @endif>
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
          // dom: 'lrtipB',
          // buttons: [
          //         'copy', 'csv', 'excel', 'pdf', 'print'
          // ]
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'TUNAS ABADI 8 | Data Addtional Cost',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Addtional Cost',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Addtional Cost',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Addtional Cost',
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
