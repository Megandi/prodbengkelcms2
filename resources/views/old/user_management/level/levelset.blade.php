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
                <form action="{{ url('/usermanagement/level_home/do_add_set_menu') }}" method="post">
                <input type="hidden" name="id_level" value="{{$id}}">
                {{ csrf_field() }}
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>View</th>
                        <th>Create</th>
                        <th>Update</th>
                        <th>Delete</th>
                        <th>Export</th>
                      </tr>
                    </thead>
                    <tbody>
                      
                      @foreach($level_akses as $item)
                      <tr>
                        <td>{{ $item->nama }}</td>
                        <td><input type="checkbox" name="read_{{$item->id_menus}}" {{$item->r==1?"checked":""}}></td>
                        <td><input type="checkbox" name="create_{{$item->id_menus}}" {{$item->c==1?"checked":""}}></td>
                        <td><input type="checkbox" name="update_{{$item->id_menus}}" {{$item->u==1?"checked":""}}></td>
                        <td><input type="checkbox" name="delete_{{$item->id_menus}}" {{$item->d==1?"checked":""}}></td>
                        <td><input type="checkbox" name="export_{{$item->id_menus}}" {{$item->e==1?"checked":""}}></td>
                      </tr>
                      @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Name</th>
                        <th>View</th>
                        <th>Create</th>
                        <th>Update</th>
                        <th>Delete</th>
                        <th>Export</th>
                      </tr>
                    </tfoot>
                  </table>
                
                </div>

                <div class="box-footer">
                  <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                  <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this data ?')">Submit</button>
                </div>
                </form>
              </div>
            </div>
          </div>
      </section>

		</div>
	</div>

  <script>
      function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/usermanagement/level_home') }}");
      }
    }

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
          dom: 'lrtipB',
          buttons: [
                  'copy', 'csv', 'excel', 'pdf', 'print'
          ]
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

      });
    </script>
<body>
@endsection