@extends('template.app')

{{-- set title --}}
@section('title', 'Buying Checkout')

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
          Buying
          <small>Checkout</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Buying Add</li>
          <li class="active">Checkout</li>
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
                  <h3 class="box-title">Total : Rp {{number_format($totaluang)}}</h3>
                </div>
              </div>

              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="box box-default">
                      <div class="box-header">
                      {{-- disini total detail penjualan dari total semua qty --}}
                        <h3 class="box-title">Total Buying : Rp {{number_format($total)}} | </h3>
                        @if($total-$totaluang<0)
                          <h3 class="box-title">Cash Back Total : Rp {{number_format(($total-$totaluang)*-1)}}</h3>
                        @else
                          <h3 class="box-title">Grand Total : Rp {{number_format($total-$totaluang)}}</h3>
                        @endif
                      </div>
                      <div class="box-body table-responsive">
                        <table id="table-home" class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Items</th>
                              <th>Quantities</th>
                              <th>Sub Price</th>
                              <th>Sell Price</th>
                              <th>Date Buy</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($buying->detail as $detail)
                            <tr>
                              @if($detail->status == "T")
                                <td>{{$detail->barang_temp->nama}}</td>
                              @else
                                <td>{{$detail->barang->nama}}</td>
                              @endif
                              <td>{{$detail->qty}}</td>
                              <td>{{number_format($detail->sub_total_pembelian)}}</td>
                              <td>{{number_format($detail->sub_total_pembelian * $detail->qty)}}</td>
                              <td>{{$detail->created_date}}</td>
                            </tr>
                            @endforeach

                          </tbody>
                          <tfoot>
                            <tr>
                              <th>ID Items</th>
                              <th>Quantities</th>
                              <th>Sub Price</th>
                              <th>Sell Price</th>
                              <th>Date Buy</th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>

                <form class="form-horizontal" action="{{ url('/finance/buying_home/tig/do_checkout/') }}" method="post">
                    <div class="form-group">
                      <label for="input_sell_nota" class="col-sm-2 control-label">No Nota</label>
                      <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="buyingid" id="pembelian_id" value="{{$buying->id}}">
                        <input type="hidden" class="form-control" name="total" id="total" value="{{$total}}">
                        <input type="hidden" class="form-control" name="totaluang" id="total" value="{{$totaluang}}">
                        <input type="text" required class="form-control" name="no_nota" id="sell_nota" placeholder="Nota" value="{{old('sell_nota')}}">
                        <input type="hidden" id="idretur" name="idretur" value="{{$idretur}}">
                      @if($errors->has('sell_nota'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('sell_nota') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_supp_id" class="col-sm-2 control-label">Supplier <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-supp-id form-control">
                          <option selected>{{old('supp_id')}} - {{old('supp_name')}}</option>
                        </select>
                       @if($errors->has('supp_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('supp_id') }}</p>
                      @endif
                      <input type="hidden" id="supp_id" name="supp_id" value="{{old('supp_id')}}">
                      <input type="hidden" id="supp_name" name="supp_name" value="{{old('supp_name')}}">
                      <input type="hidden" id="supp_deposit" name="supp_deposit" value="{{old('supp_deposit')}}">
                      </div>
                    </div>

                    {{--<div class="form-group">
                      <label for="input_buy_date" class="col-sm-2 control-label">Date Buy <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker1'>
                          <input type="text" class="form-control for_date" name="buy_date" id="buy_date" required>
                          @if($errors->has('buy_date'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('buy_date') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>--}}

                    <div class="form-group">
                      <label for="input_status_payment" class="col-sm-2 control-label">Status Payment <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="status_payment" id="status_payment" required>
                          <option value="">Choose Status Payment</option>
                          <option value="1">Paid</option>
                          <option value="0">Unpaid</option>
                        </select>
                      @if($errors->has('status_payment'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('status_payment') }}</p>
                      @endif
                      </div>
                    </div>

                    <div id="for_unpaid" style="display: none;">
                    <div class="form-group">
                      <label for="input_sell_dp" class="col-sm-2 control-label">Down Payment <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" max="{{$total}}" min="0" class="form-control" name="sell_dp" id="sell_dp" placeholder="Down Payment" value="{{old('sell_dp')}}">
                      @if($errors->has('sell_dp'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('sell_dp') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_due_date" class="col-sm-2 control-label">Due Date <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class="input-group date" id="datetimepicker">
                          <input type="text" class="form-control for_date" name="sell_due_date" id="sell_due_date" value="">
                        </div>
                      </div>
                    </div>
                    </div>

                    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">

                  </div>
                  <div class="box-footer">
                    <a href="{{ url('finance/buying_home/tig/addnext/'.$buying->id) }}" style="width:90px;" type="submit" class="btn btn-default">Back</a>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to update this data ?')">Submit</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
      </div>
    </section>

	</div>

	<script>

  var status_payment = "x";
    $("#status_payment").on('change',function(e) {
      if(e.target.value==1){
        status_payment = 1;
        $("#sell_dp").val({{$total}});
        $("#for_unpaid").hide();
      }else{
        status_payment = 0;
        $("#for_unpaid").show();
        $("#sell_dp").val(0);
      }
    });

    $("#sell_dp").on('keypress',function() {
      if(status_payment==0){
        if($("#sell_dp").val()>{{$total}}){
          $("#sell_dp").val({{$total}});
          alert('Max Down Payment : Rp {{$total}}');
          $("#sell_dp").val({{$total}});
        }
      }
    });

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/finance/buying_home') }}");
      }
    }

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      minDate: new Date()
    });

    // supplier select2
    $(".js-supp-id").select2({
    ajax: {
      url: "{{ url('/finance/buying_home/search_supp_buying') }}",
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

    $(".js-supp-id").on("select2:select", function (e) {
      var obj = $(".js-supp-id").select2("data")
      $('#supp_id').val(obj[0].id);
      $('#supp_name').val(obj[0].name);
      $('#supp_deposit').val(obj[0].deposit);
    });
    // supplier select2

	</script>
</body>
@endsection
