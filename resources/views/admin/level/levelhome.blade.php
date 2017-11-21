@extends('template.app')

{{-- set title --}}
@section('title', 'Level List')

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
          Level
          <small>Home</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li class="active">Level Home</li>
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

            {{-- tables --}}
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Data Level</h3>
                </div>

                <div class="box-body table-responsive">
                  
                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->c==1)
                  <a href="{{ url('/admin/level_home/add') }}" type="button" class="btn btn-primary">Add New Level</a><br><br>
                  @endif
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>Name</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>

                        <th>Action</th>
                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <th>Action</th>
                        @endif
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach($lt_user_type as $item)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->created_date));?></td>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->modify_user_id }}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($item->last_modify_date));?></td>
                        <td>@if($item->status == 'A') {{ 'Active' }} @elseif($item->status == 'D') {{ 'Deactive' }} @elseif($item->status == 'T') {{ 'Temporary' }} @endif</td>
                        <td><a style="width:90px;" href="{{ url('/admin/level_home/set_menu/'.$item->id) }}" type="button" class="btn btn-warning">Set Menu</a></td>
                 
                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <td><a style="width:90px;" href="{{ url('/admin/level_home/edit/'.$item->id) }}" type="button" class="btn btn-info">Edit</a></td>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->d==1)
                        <td><a style="width:90px;" href="{{ url('/admin/level_home/delete/'.$item->id) }}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>
                        @endif
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>Name</th>
                        <th>Modify by</th>
                        <th>Last Modified</th>
                        <th>Status</th>

                        <th>Action</th>
                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->u==1)
                        <th>Action</th>
                        @endif

                @if(DB::table('level_akses')->where('id_menu', '4')->where('id_level',auth::user()->level_id)->first()->d==1)
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
                  title: 'TUNAS ABADI 8 | Data Level',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Level',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Data Level',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Data Level',
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