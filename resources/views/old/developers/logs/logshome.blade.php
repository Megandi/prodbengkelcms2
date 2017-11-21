@extends('template.app')

{{-- set title --}}
@section('title', 'Logs List')

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
          Logs
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Logs Home</li>
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
            <div class="row">
              <form class="form" action="{{ url('/developers/logs_home/getrange') }}" method="post">
                <div class="box-body">
                {{-- set token --}}
                {{ csrf_field() }}

                  <div class="form-group">
                    <label for="startdate_input">Start Date</label>
                    <div class="date">
                      <input type="text" class="form-control for_date" id="start_date" name="start_date" placeholder="Start Date" value="<?php if(isset($arraydate[0])){ echo $arraydate[0]; }?>">
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="enddate_input">End Date</label>
                    <div class="date">
                      <input type="text" class="form-control for_date" id="end_date" name="end_date" placeholder="End Date" value="<?php if(isset($arraydate[1])){ echo $arraydate[1]; }?>">
                    </div>
                  </div>
                  <div class="pull-right">
                    <a style="width:90px;" href="{{ url('/developers/logs_home') }}" type="button" class="btn btn-warning">Reset</a>
                    <button style="width:90px;" type="submit" class="btn btn-primary">Submit</button>
                  </div>
                </div>
              </form>
            </div>
            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Logs</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>Do</th>
                        <th>Table</th>
                        <th>Primary Key</th>
                        <th>Notes</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                         @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach($logs as $item)
                      <tr>
                        <td><?= date("l, d F Y | H:i:s",strtotime($item->created_date));?></td>
                        <td>{{ $item->do }}</td>
                        <td>{{ $item->table }}</td>
                        <td>{{ $item->primary }}</td>
                        <td>{{ $item->note }}</td>
                        <td>{{ $item->modify_user_id }} - {{ $item->name }}</td>
                        <td><?= date("l, d F Y | H:i:s",strtotime($item->last_modify_date));?></td>
                        @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <td><button style="width:90px;" class="btn btn-info btn-notes" data-toggle="modal" data-target="#modal_notes" txt-logs="{{$item->note}}">Edit</button></td>
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>Do</th>
                        <th>Table</th>
                        <th>Primary Key</th>
                        <th>Notes</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                         @if(DB::table('level_akses')->where('id_menu', '2')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </tfoot>
                  </table>
                </div>

              </div>
            </div>
          </div>
      </section>

		</div>
	</div>

  <!-- Modal -->
  <div class="modal fade" id="modal_notes" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">Notes</h4>
        </div>
        <form class="form-horizontal" action="{{ url('/developers/logs_home/do_edit/'.$item->id) }}" method="post">
          <div class="modal-body">

          {{-- set token --}}
          {{ csrf_field() }}

            <div class="form-group">
              <label for="input_notes" class="col-sm-2 control-label">Notes <span style="color:red;"></span></label>
              <div class="col-sm-10">
                <input type="text" class="form-control" name="note" id="note" placeholder="Notes" value="{{old('note')}}">
              @if($errors->has('note'))
                  <p style="font-style: bold; color: red;">{{ $errors->first('note') }}</p>
              @endif
              </div>
            </div>
          </div>

          <input type="hidden" name="_method" id="_method" value="PUT">

          <div class="modal-footer">
            <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary pull-right">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Modal -->

  <script>

      $('.btn-notes').on('click', function(e) {
        e.preventDefault();

        var notes = $(this).attr('txt-logs');
        $('#note').val(notes);

      });

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
                  title: 'TUNAS ABADI 8 | Data Logs',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Logs',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Data Logs',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Logs',
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