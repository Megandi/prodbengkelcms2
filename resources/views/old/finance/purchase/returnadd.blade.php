@extends('template.app')

{{-- set title --}}
@section('title', 'Purchase Return Add')

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
          Purchase Return
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Purchase Return Home</li>
          <li class="active">Add</li>
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

                <form class="form-horizontal" action="{{ url('/finance/return_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_return_id" class="col-sm-2 control-label">ID Purchase Return <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                         <input type="text" class="form-control" name="return_id" id="return_id" placeholder="ID Purchase Return" value="{{$idreturn}}" readonly="true">
                      @if($errors->has('return_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('return_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_pembelian_id" class="col-sm-2 control-label">ID Buying <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" required class="js-pembelian-id form-control">
                          <option selected>{{old('buy_id')}}</option>
                        </select>
                       @if($errors->has('pembelian_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('pembelian_id') }}</p>
                      @endif
                      <input type="hidden" id="buy_id" name="buy_id" value="{{old('buy_id')}}">
                      <input type="hidden" id="items_id" name="items_id" value="{{old('items_id')}}">
                      <input type="hidden" id="items_qty" name="items_qty" value="{{old('items_qty')}}">
                      <input type="hidden" id="items_sub_total" name="items_sub_total" value="{{old('items_sub_total')}}">
                      <input type="hidden" id="payment_total" name="payment_total" value="{{old('payment_total')}}">
                      </div>
                    </div>
                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right">Submit</button>
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
        window.location.replace("{{ url('/finance/return_home') }}");
      }
    }

    // items select2
    $(".js-pembelian-id").select2({
    ajax: {
      url: "{{ url('/finance/return_home/search_buying') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using carom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-pembelian-id").on("select2:select", function (e) {
      var obj = $(".js-pembelian-id").select2("data")
      $('#buy_id').val(obj[0].id);

      $('#items_id').val(obj[0].items_id);
      $('#items_qty').val(obj[0].qty);
      $('#items_sub_total').val(obj[0].sub_total);

      $('#items_id_view').val(obj[0].items_id);
      $('#items_qty_view').val(obj[0].qty);
      $('#items_sub_total_view').val(obj[0].sub_total);

      $('#payment_total').val(obj[0].qty * obj[0].sub_total);
      $('#payment_total_view').val(obj[0].qty * obj[0].sub_total);
    });
    // items select2

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD'
    });

  </script>
</body>
@endsection
