@extends('template.app')

{{-- set title --}}
@section('title', 'Selling Return Edit')

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
          Selling Return
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Selling Return Home</li>
          <li class="active">Edit</li>
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
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/finance/salreturn_home/do_edit/'.$tr_returpenjualan->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_return_id" class="col-sm-2 control-label">ID Selling Return <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="return_id" id="return_id" placeholder="ID Selling Return" value="{{ $tr_returpenjualan->retur_penjualan_id }}">
                      @if($errors->has('return_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('return_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_pembelian_id" class="col-sm-2 control-label">ID Selling <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="pembelian_id" id="pembelian_id" placeholder="ID Buying" value="{{ $tr_returpenjualan->penjualan_id }}" readonly>
                      @if($errors->has('pembelian_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('pembelian_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_id" class="col-sm-2 control-label">ID Items <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="items_id" id="items_id" placeholder="ID Buying" value="{{ $tr_returpenjualan->barang_id }} - {{$tr_returpenjualan->items_id}}" readonly>
                      @if($errors->has('items_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_type_return" class="col-sm-2 control-label">Type Return <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="type_return" id="type_return">
                          <option value="">Choose Type Return</option>
                          <option value="1">Cut the Debt</option>
                          <option value="2">Change Money</option>
                          <option value="3">Replace items at the same price</option>
                          <option value="4">Trade-in Goods</option>
                        </select>
                      @if($errors->has('type_return'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('type_return') }}</p>
                      @endif
                      </div>
                    </div>

                    <input type="hidden" name="_method" id="_method" value="PUT">

                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this transaction ?')">Submit</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
      </div>
    </section>

  </div>

  <script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/finance/salreturn_home') }}");
      }
    }

  </script>
</body>
@endsection
