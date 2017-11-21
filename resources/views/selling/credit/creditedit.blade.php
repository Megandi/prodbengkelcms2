@extends('template.app')

{{-- set title --}}
@section('title', 'Credit Edit')

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
          Credit
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Credit Home</li>
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

                <form class="form-horizontal" action="{{ url('/selling/credit_home/do_edit') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_debt_id" class="col-sm-2 control-label">ID Credit <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="debt_id_view" id="debt_id_view" placeholder="Debt ID" value="{{ $lt_piutang->piutang_id }}" readonly>
                        <input type="hidden" class="form-control" name="credit_id" id="credit_id" placeholder="Credit ID" value="{{ $lt_piutang->id }}">
                      @if($errors->has('debt_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('debt_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_buy_id" class="col-sm-2 control-label">ID Selling <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="buy_id_view" id="buy_id_view" placeholder="ID Buying" value="{{ $lt_piutang->penjualan->penjualan_id }}" readonly>
                      @if($errors->has('buy_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('buy_id') }}</p>
                      @endif
                      <input type="hidden" id="buy_id" name="buy_id" value="{{$lt_piutang->penjualan_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_sub_total" class="col-sm-2 control-label">Total <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="sub_total_view" value="{{ number_format($lt_piutang->total) }}" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_payment_total" class="col-sm-2 control-label">Already Paid <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="payment_total_view" value="{{ number_format($lt_piutang->bayar) }}" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_payment_total" class="col-sm-2 control-label">Must Paid <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="payment_total_view" value="{{ number_format($lt_piutang->total-$lt_piutang->bayar) }}" readonly>
                        <input type="hidden" class="form-control" name="mustpaid" value="{{$lt_piutang->total-$lt_piutang->bayar}}" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_total" class="col-sm-2 control-label">Pay <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" required class="form-control" name="pay" id="pay" placeholder="Total Credit payable">
                      @if($errors->has('pay'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('pay') }}</p>
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
        window.location.replace("{{ url('/selling/credit_home') }}");
      }
    }

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD'
    });

  </script>
</body>
@endsection
