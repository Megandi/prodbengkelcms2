@extends('template.app')

{{-- set title --}}
@section('title', 'Selling Add')

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
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Selling Home</li>
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

                <form class="form-horizontal" action="{{ url('/finance/selling_home/do_add') }}" method="post" onsubmit="return validasitrue();">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_type_id" class="col-sm-2 control-label">Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="form-control" name="type_select" id="type_select" required>
                          <option value="">Select Type</option>
                          <option @if(old('type_select') == '1') selected @endif value="1">Service</option>
                          <option @if(old('type_select') == '2') selected @endif value="2">Items</option>
                        </select>
                      <input type="hidden" id="type_id" name="type_id" value="1">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_id" class="col-sm-2 control-label">Items or Services <span style="color:red;">*</span></label>
                      <div class="col-sm-10">

                        <select style="text-transform:uppercase" class="js-items-id form-control" required>
                          <option selected>{{old('items_id')}} - {{old('name')}}</option>
                        </select>

                        @if($errors->has('items_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_id') }}</p>
                        @endif

                        <input type="hidden" id="items_id" name="items_id" value="{{old('items_id')}}">
                        <input type="hidden" id="items_qty" name="items_qty" value="{{old('items_qty')}}">
                        <input type="hidden" id="items_qty_buy" name="items_qty_buy" value="{{old('items_qty_buy')}}">
                        <input type="hidden" id="items_sub_total" name="items_sub_total" value="{{old('items_sub_total')}}">
                        <input type="hidden" id="items_grand_total" name="items_grand_total" value="{{old('items_grand_total')}}">
                        <input type="hidden" id="id_get_items" name="id_get_items" value="{{old('id_get_items')}}">
                        <input type="hidden" id="id_get_stock" name="id_get_stock" value="{{old('id_get_stock')}}">
                        <input type="hidden" id="name" name="name" value="{{old('name')}}">
                        <input type="hidden" id="inven" name="inven" value="{{old('inven')}}">
                        <input type="hidden" id="isavailable" name="isavailable">

                      </div>
                    </div>

                    <div id="new_item_layout" style="display: none;">
                      <div class="form-group">
                        <label for="input_new_item" class="col-sm-2 control-label">New Item </label>
                        <div class="col-sm-10">
                          <input type="text" class="form-control" name="new_item" id="new_item" placeholder="New Item" value="{{old('new_item')}}">
                          @if($errors->has('new_item'))
                              <p style="font-style: bold; color: red;">{{ $errors->first('new_item') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_qty_view" class="col-sm-2 control-label">Quantities <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" required min="0" class="form-control" name="items_qty_view" id="items_qty_view" placeholder="Quantities" value="{{old('items_qty_view')}}" onblur="calculateForm();">
                        <input type="hidden" min="0" class="form-control" name="items_qty_viewasli" id="items_qty_viewasli" value="{{old('items_qty_viewasli')}}">
                      @if($errors->has('items_qty_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_qty_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp1_id" class="col-sm-2 control-label">Employee ID 1 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-emp1-id form-control">
                          <option selected>{{old('emp1_id')}} - {{old('emp1_name')}}</option>
                        </select>
                       @if($errors->has('emp1_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp1_id') }}</p>
                      @endif
                      <input type="hidden" id="emp1_id" name="emp1_id" value="{{old('emp1_id')}}">
                      <input type="hidden" id="emp1_name" name="emp1_name" value="{{old('emp1_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp2_id" class="col-sm-2 control-label">Employee ID 2 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-emp2-id form-control">
                          <option selected>{{old('emp2_id')}} - {{old('emp2_name')}}</option>
                        </select>
                       @if($errors->has('emp2_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp2_id') }}</p>
                      @endif
                      <input type="hidden" id="emp2_id" name="emp2_id" value="{{old('emp2_id')}}">
                      <input type="hidden" id="emp2_name" name="emp2_name" value="{{old('emp2_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp3_id" class="col-sm-2 control-label">Employee ID 3 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-emp3-id form-control">
                          <option selected>{{old('emp3_id')}} - {{old('emp3_name')}}</option>
                        </select>
                       @if($errors->has('emp3_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp3_id') }}</p>
                      @endif
                      <input type="hidden" id="emp3_id" name="emp3_id" value="{{old('emp3_id')}}">
                      <input type="hidden" id="emp3_name" name="emp3_name" value="{{old('emp3_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp4_id" class="col-sm-2 control-label">Employee ID 4 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-emp4-id form-control">
                          <option selected>{{old('emp4_id')}} - {{old('emp4_name')}}</option>
                        </select>
                       @if($errors->has('emp4_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp4_id') }}</p>
                      @endif
                      <input type="hidden" id="emp4_id" name="emp4_id" value="{{old('emp4_id')}}">
                      <input type="hidden" id="emp4_name" name="emp4_name" value="{{old('emp4_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp5_id" class="col-sm-2 control-label">Employee ID 5 <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-emp5-id form-control">
                          <option selected>{{old('emp5_id')}} - {{old('emp5_name')}}</option>
                        </select>
                       @if($errors->has('emp5_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp5_id') }}</p>
                      @endif
                      <input type="hidden" id="emp5_id" name="emp5_id" value="{{old('emp5_id')}}">
                      <input type="hidden" id="emp5_name" name="emp5_name" value="{{old('emp5_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_sub_total_view" class="col-sm-2 control-label">Sub Price <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="items_sub_total_view" id="items_sub_total_view" placeholder="Sub Total" value="{{old('items_sub_total_view')}}" onblur="calculateForm();" required>
                      @if($errors->has('items_sub_total_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_sub_total_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_grand_total_view" class="col-sm-2 control-label">Grand Total <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="items_grand_total_view" id="items_grand_total_view" placeholder="Grand Total" value="{{old('items_grand_total_view')}}" readonly>
                      @if($errors->has('items_grand_total_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_grand_total_view') }}</p>
                      @endif
                      </div>
                    </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right">Submit</button>
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
    if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
      window.location.replace("{{ url('/finance/selling_home') }}");
    }
  }

  function validasitrue(){
        if($("#isavailable").val()==1){
            return confirm('Are you sure you want to save this data Inventory ?')
            return true;
        }
        else {
            return confirm('Are you sure you want to save this data ?')
            return true;
        }
    }

  $("#type_select").on('change',function (e) {
      $("#type_id").val($("#type_select").val());
      if($("#type_id").val()==1){
        $("#sell_qty").attr('readonly',true);
        $("#layout_sell_qty").css('display','none');
        $("#layout_items_qty_view").css('display','none');
        $("#sell_pricediv").css('display','none');
      }else{
        $("#sell_pricediv").css('display','block');
        $("#layout_sell_qty").css('display','block');
        $("#layout_items_qty_view").css('display','block');
        $("#sell_qty").val("");
        $("#sell_qty").attr('readonly',false);
        $("#items_qty_view").val("");
      }
  });

  // items select2
  $(".js-items-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_items_selling') }}",
    dataType: 'json',
    delay: 250,
    data: function (params) {
      return {
        q: params.term, // search term
        type: $("#type_id").val() // search term
      };
    },
    processResults: function (data) {
      // parse the results into the format expected by Select2.
      // since we are using carom formatting functions we do not need to
      // alter the remote JSON data

      // console.log(data);

      // data.push({id:'addnew',text:'Add New'});


        return {
          results: data
        };
      },
      cache: true
    },
    minimumInputLength: 2,
  });


  $(".js-items-id").on("select2:select", function (e) {
    if($("#type_id").val()==1||$("#type_id").val()==2){
      var obj = $(".js-items-id").select2("data")
      $('#items_id').val(obj[0].id);
      $('#items_qty').val(obj[0].qty);
      $('#items_qty_buy').val(obj[0].qty_buy);
      $('#items_sub_total').val(obj[0].sub_total);
      $('#items_sellprice').val(obj[0].sell_price);
      $('#name').val(obj[0].name);
      $('#isavailable').val(obj[0].inven);

      $('#items_qty_view').val(obj[0].qty);
      $('#items_qty_viewasli').val(obj[0].qty);
      $('#items_sub_total_view').val(obj[0].sell_price);

      $('#items_grand_total').val(obj[0].qty * obj[0].sub_total);
      $('#items_grand_total_view').val(obj[0].qty * obj[0].sub_total);

      $('#id_get_items').val(obj[0].id_get_items);
      $('#id_get_stock').val(obj[0].id_get_stock);
      if(obj[0].id=="addnew"){
        $("#new_item_layout").show();
      }else{
        $("#new_item_layout").hide();
      }

      var grand = document.getElementById("items_qty_view").value * document.getElementById("items_sub_total_view").value;
      document.getElementById("items_grand_total_view").value = Math.round(grand);
    }
  });
  // items select2

  // customer select2
  $(".js-cust-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_customer_selling') }}",
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

  $(".js-cust-id").on("select2:select", function (e) {
    var obj = $(".js-cust-id").select2("data")
    $('#cust_id').val(obj[0].id);
  });
  // customer select2

  // employee 1 select2
  $(".js-emp1-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_employee_1') }}",
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

  $(".js-emp1-id").on("select2:select", function (e) {
    var obj = $(".js-emp1-id").select2("data")
    $('#emp1_id').val(obj[0].id);
    $('#emp1_name').val(obj[0].name);
  });
  // employee 1 select2

  // employee 2 select2
  $(".js-emp2-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_employee_2') }}",
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

  $(".js-emp2-id").on("select2:select", function (e) {
    var obj = $(".js-emp2-id").select2("data")
    $('#emp2_id').val(obj[0].id);
    $('#emp2_name').val(obj[0].name);
  });
  // employee 2 select2

  // employee 3 select2
  $(".js-emp3-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_employee_3') }}",
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

  $(".js-emp3-id").on("select2:select", function (e) {
    var obj = $(".js-emp3-id").select2("data")
    $('#emp3_id').val(obj[0].id);
    $('#emp3_name').val(obj[0].name);
  });
  // employee 3 select2

  // employee 4 select2
  $(".js-emp4-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_employee_4') }}",
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

  $(".js-emp4-id").on("select2:select", function (e) {
    var obj = $(".js-emp4-id").select2("data")
    $('#emp4_id').val(obj[0].id);
    $('#emp4_name').val(obj[0].name);
  });
  // employee 4 select2

  // employee 5 select2
  $(".js-emp5-id").select2({
  ajax: {
    url: "{{ url('/finance/selling_home/search_employee_5') }}",
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

  $(".js-emp5-id").on("select2:select", function (e) {
    var obj = $(".js-emp5-id").select2("data")
    $('#emp5_id').val(obj[0].id);
    $('#emp5_name').val(obj[0].name);
  });
  // employee 5 select2

  var calculateForm = function (){
    var grand = document.getElementById("items_qty_view").value * document.getElementById("items_sub_total_view").value;
    document.getElementById("items_grand_total_view").value = Math.round(grand);
  };

  $('.for_date').datetimepicker({
    format: 'YYYY/MM/DD',
    maxDate: new Date()
  });

   $('.for_date_due').datetimepicker({
    format: 'YYYY/MM/DD',
    minDate: new Date()
  });

  </script>
</body>
@endsection
