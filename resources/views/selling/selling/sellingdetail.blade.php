@extends('template.app')

{{-- set title --}}
@section('title', 'Selling Detail')

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
          Selling
          <small>Detail</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Selling Home</li>
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
                    <label for="input_items_id" class="col-sm-2 control-label">Buying ID <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['penjualan_id']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Date <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['tanggal']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">No Nota <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['no_nota']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Supplier ID <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['customer_id']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Total Price <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['penjualan_total']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Last Modified <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['last_modify_date']}}" >
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="input_items_id" class="col-sm-2 control-label">Modify By <span style="color:red;"></span></label>
                    <div class="col-sm-10">
                      <input type="text" required class="form-control" disabled value="{{ $selling['modify_user_id']}}" >
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
                  <h3 class="box-title">Detail Selling</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Created Date</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Sub Total Price</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($selling->detail as $row)
                      <tr>
                        <td><?= date("d/m/Y H:i:s",strtotime($row->created_date));?></td>
                        <td>{{$row->barang->nama}}</td>
                        <td>{{ $row->qty }}</td>
                        <td>{{ number_format($row->sub_total_pembelian)}}</td>
                      </tr>
                      @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Created Date</th>
                        <th>Item</th>
                        <th>Qty</th>
                        <th>Sub Total Price</th>
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
      if (confirm("Are you sure you want to close this page ? after you close this page, you can continue this buying !")) {
        window.location.replace("{{ url('/selling/selling_home') }}");
      }
    }
  </script>
</body>
@endsection
