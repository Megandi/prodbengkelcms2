@extends('template.app')

{{-- set title --}}
@section('title', 'Addtional Detail')

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
          Addtional
          <small>Detail</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Addtional Home</li>
          <li class="active">Detail</li>
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

              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title"></h3>
                </div>
                <form class="form-horizontal">
                <div class="box-body">
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Addtional ID <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add['biayalain_id']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Created Date <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add['created_date']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">No Nota <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add['no_nota']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Total Qty <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add->detail->SUM('jumlah')}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Amount Cost <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ number_format($add->total_biaya) }}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Last Modified <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add['last_modify_date']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Modify By <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $add['modify_user_id']}}" >
                    </div>
                  </div>
                </div>
                </form>
                <div class="box-footer">
                  <button style="width:90px;" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                </div>
              </div>

              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">Detail add</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Price</th>
                        <th>Created Date</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($add->detail as $row)
                      <tr>
                        <td>{{$row->nama}}</td>
                        <td>{{$row->kategori}}</td>
                        <td>{{ $row->jumlah }}</td>
                        <td>{{ number_format($row->harga)}}</td>
                        <td><?= date("d/m/Y H:i:s",strtotime($row->created_date));?></td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Price</th>
                        <th>Created Date</th>
                      </tr>
                    </tfoot>
                  </table>
                  </div>

              </div>
            </div>
          </div>
    </section>

  </div>

  <script>
    function close_window() {
      if (confirm("Are you sure you want to close this page ? after you close this page, you can continue this add !")) {
        window.location.replace("{{ url('/addtional/addtional_home') }}");
      }
    }
  </script>
</body>
@endsection
