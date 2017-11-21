@extends('template.app')

{{-- set title --}}
@section('title', 'Buying Add')

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
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Buying Home</li>
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
                  <h3 class="box-title">Please complete the form of first item before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/buying/buying_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_items_id" class="col-sm-2 control-label">ID Items <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-items-id form-control">
                          <option selected>{{old('items_id')}} - {{old('name')}}</option>
                        </select>

                        @if($errors->has('items_id'))
                        <p style="font-style: bold; color: red;">{{ $errors->first('items_id') }}</p>
                        @endif

                        <input type="hidden" id="items_id" name="items_id" value="{{old('items_id')}}">
                        <input type="hidden" id="items_qty" name="items_qty" value="{{old('items_qty')}}">
                        <input type="hidden" id="items_sub_total" name="items_sub_total" value="{{old('items_sub_total')}}">
                        <input type="hidden" id="items_grand_total" name="items_grand_total" value="{{old('items_grand_total')}}">
                        <input type="hidden" id="id_get_items" name="id_get_items" value="{{old('id_get_items')}}">
                        <input type="hidden" id="id_get_stock" name="id_get_stock" value="{{old('id_get_stock')}}">
                        <input type="hidden" id="name" name="name" value="{{old('name')}}">
                      </div>
                    </div>

                    <div id="new_item_layout" style="display: none;">
                      <div class="form-group">
                        <label for="input_new_item" class="col-sm-2 control-label">New Item </label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control name" name="new_item" id="new_item" placeholder="New Item" value="{{old('new_item')}}">
                        @if($errors->has('new_item'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('new_item') }}</p>
                        @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_qty_view" class="col-sm-2 control-label">Quantities <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="items_qty_view" id="items_qty_view" placeholder="Quantities" value="{{old('items_qty_view')}}" onblur="calculateForm();" required>
                      @if($errors->has('items_qty_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_qty_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_sub_total_view" class="col-sm-2 control-label">Sub Price <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="items_sub_total_view" id="items_sub_total_view" placeholder="Sub Price" value="{{old('items_sub_total_view')}}" onblur="calculateForm();" required>
                      @if($errors->has('items_sub_total_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_sub_total_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_grand_total_view" class="col-sm-2 control-label">Grand Total <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" required class="form-control" name="items_grand_total_view" id="items_grand_total_view" placeholder="Grand Total" value="{{old('items_grand_total_view')}}" readonly>
                      @if($errors->has('items_grand_total_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_grand_total_view') }}</p>
                      @endif
                      </div>
                    </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this data ?')">Submit</button>
                  </div>
                  <input type="hidden" name="validasi" value="{{ $validasi }}">
                </form>
              </div>
            </div>
        </div>
      </div>
    </section>

  </div>

  <script>

    function close_window() {
      if (confirm("Are you sure you want to close this page ? after you close this page, you can continue this buying !")) {
        window.location.replace("{{ url('/buying/buying_home') }}");
      }
    }

    var status_payment = "x";
    $("#status_payment").on('change',function(e) {
      if(e.target.value==1){
        status_payment = 1;
        $("#for_unpaid").hide();
        $("#for_unpaid_dp").hide();
      }else{
        status_payment = 0;
        $("#for_unpaid").show();
        $("#for_unpaid_dp").show();
      }
    });

    // items select2
    $(".js-items-id").select2({
    ajax: {
      url: "{{ url('/buying/buying_home/search_items_buying') }}",
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

    $(".js-items-id").on("select2:select", function (e) {
      var obj = $(".js-items-id").select2("data")
      $('#items_id').val(obj[0].id);
      $('#items_qty').val(obj[0].qty);
      $('#items_sub_total').val(obj[0].sub_total);
      $('#items_sellprice').val(obj[0].sell_price);
      $('#name').val(obj[0].name);

      $('#items_qty_view').val(obj[0].qty);
      $('#items_sub_total_view').val(obj[0].sub_total);

      $('#items_grand_total').val(obj[0].qty * obj[0].sub_total);
      $('#items_grand_total_view').val(obj[0].qty * obj[0].sub_total);

      $('#id_get_items').val(obj[0].id_get_items);
      $('#id_get_stock').val(obj[0].id_get_stock);
      if(obj[0].id=="addnew"){
        $("#new_item_layout").show();
      }else{
        $("#new_item_layout").hide();
      }
    });
    // items select2

    // supplier select2
    $(".js-supp-id").select2({
    ajax: {
      url: "{{ url('/buying/buying_home/search_supp_buying') }}",
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

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date()
    });

     $('.for_date_due').datetimepicker({
      format: 'YYYY/MM/DD',
      minDate: new Date()
    });

    var calculateForm = function (){
      var grand = document.getElementById("items_qty_view").value * document.getElementById("items_sub_total_view").value;
      document.getElementById("items_grand_total_view").value = Math.round(grand);
    };

  </script>
</body>
@endsection
