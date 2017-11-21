@extends('template.app')

{{-- set title --}}
@section('title', 'Addtional Checkout')

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
          <small>Checkout</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Cost Addtional</li>
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
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="box box-default">
                      <div class="box-header">
                      {{-- disini total detail penjualan dari total semua qty --}}
                        <h3 class="box-title">Total Qty : {{$total}} |</h3>
                        <h3 class="box-title">Total Amount : {{number_format($totalbiaya)}}</h3>
                      </div>
                      <div class="box-body table-responsive">
                        <table id="table-home" class="table table-bordered table-hover">
                          <thead>
                            <tr>
                              <th>Name</th>
                              <th>Category</th>
                              <th>Total</th>
                              <th>Sub Price</th>
                            </tr>
                          </thead>
                          <tbody>

                            @foreach($biayalain->detail->where('status', 'A') as $detail)
                            <tr>
                              <td>{{$detail->nama}}</td>
                              <td>{{$detail->kategori}}</td>
                              <td>{{$detail->jumlah}}</td>
                              <td>{{number_format($detail->harga)}}</td>
                            </tr>
                           @endforeach

                          </tbody>
                          <tfoot>
                            <tr>
                              <th>Name</th>
                              <th>Category</th>
                              <th>Total</th>
                              <th>Sub Price</th>
                            </tr>
                          </tfoot>
                        </table>
                      </div>
                    </div>

                <form class="form-horizontal" action="{{ url('/addtional/addtional_home/do_checkout/') }}" method="post">
                    <div class="form-group">
                      <label for="input_sell_nota" class="col-sm-2 control-label">No Nota</label>
                      <div class="col-sm-10">
                        <input type="hidden" class="form-control" name="biayalainid" id="pembelian_id" value="{{$biayalain->id}}">
                        <input type="hidden" class="form-control" name="total" id="total" value="{{$total}}">
                        <input type="text" class="form-control" name="no_nota" id="sell_nota" placeholder="Nota" value="{{old('sell_nota')}}">
                      @if($errors->has('sell_nota'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('sell_nota') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_buy_date" class="col-sm-2 control-label">Date <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker1'>
                          <input type="text" class="form-control for_date" name="buy_date" id="buy_date" value="{{old('buy_date')}}" required>
                          @if($errors->has('buy_date'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('buy_date') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_user_id" class="col-sm-2 control-label">User Addtional <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-user-id form-control">
                          <option selected>{{old('user_id')}} - {{old('user_name')}}</option>
                        </select>
                        @if($errors->has('user_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('user_id') }}</p>
                        @endif
                      <input type="hidden" id="user_id" name="user_id" value="{{old('user_id')}}">
                      <input type="hidden" id="user_name" name="user_name" value="{{old('user_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_status_payment" class="col-sm-2 control-label">Type Addtional <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="type_addtional" id="type_addtional" required>
                          <option value="">Choose Status Payment</option>
                          <option @if(old('type_addtional') == '1') selected @endif value="1">Already</option>
                          <option @if(old('type_addtional') == '0') selected @endif value="0">Not Yet</option>
                        </select>
                      @if($errors->has('status_payment'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('status_payment') }}</p>
                      @endif
                      </div>
                    </div>

                    <div id="for_unpaid" style="display: none;">
                      <div class="form-group">
                        <label for="input_due_date" class="col-sm-2 control-label">Due Date <span style="color:red;">*</span></label>
                        <div class="col-sm-10">
                          <div class="input-group date" id="datetimepicker">
                            <input type="text" class="form-control for_date1" name="due_date" id="due_date" value="{{old('due_date')}}">
                          </div>
                        </div>
                      </div>
                    </div>

                    <input type="hidden" name="_token" id="_token" value="{{csrf_token()}}">

                  </div>
                  <div class="box-footer">
                    <a href="{{ url('finance/addtional_home/addnext/'.$biayalain->id) }}" style="width:90px;" type="submit" class="btn btn-default">Back</a>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to update this data ?')">Submit</button>
                  </div>
                </form>
              </div>
            </div>
        </div>
      </div>
    </section>
        <!-- /.content -->

	</div>

	<script>

  $("#type_addtional").on('change',function(e) {
    if(e.target.value==0){
      $("#for_unpaid").show();
    } else {
      $("#for_unpaid").hide();
    }
  });

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date()
    });

    $('.for_date1').datetimepicker({
      format: 'YYYY/MM/DD',
      minDate: new Date()
    });

    // employee 1 select2
    $(".js-user-id").select2({
    ajax: {
      url: "{{ url('/selling/selling_home/search_employee_1') }}",
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

    $(".js-user-id").on("select2:select", function (e) {
      var obj = $(".js-user-id").select2("data")
      $('#user_id').val(obj[0].id);
      $('#user_name').val(obj[0].name);
    });
    // employee 1 select2
	</script>
</body>
@endsection
