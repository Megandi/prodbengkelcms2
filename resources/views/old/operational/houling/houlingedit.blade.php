@extends('template.app')

{{-- set title --}}
@section('title', 'Houling Edit')

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
          Houling
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Houling Home</li>
          <li class="active">Edit</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/operational/houling_home/do_edit/'.$tr_houling->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_houling_id" class="col-sm-2 control-label">Houling ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="houling_id" id="houling_id" placeholder="Houling ID" value="{{$tr_houling->houling_id}}" readonly>
                      @if($errors->has('houling_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('houling_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_houling_date" class="col-sm-2 control-label">Houling Date <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                        <input type="text" class="form-control for_date" name="houling_date" id="houling_date" required>
                        @if($errors->has('houling_date'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('houling_date') }}</p>
                        @endif
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_id" class="col-sm-2 control-label">Car ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-car-id form-control">
                          <option selected>{{$tr_houling->mobil_id}}</option>
                        </select>
                       @if($errors->has('car_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_id') }}</p>
                      @endif
                      <input type="hidden" id="car_id" name="car_id" value="{{$tr_houling->mobil_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_employee_id" class="col-sm-2 control-label">Driver ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-employee-id form-control">
                          <option selected>{{$tr_houling->supir_id}}</option>
                        </select>
                       @if($errors->has('employee_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('employee_id') }}</p>
                      @endif
                      <input type="hidden" id="employee_id" name="employee_id" value="{{$tr_houling->supir_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_id" class="col-sm-2 control-label">Route ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-route-id form-control">
                          <option selected>{{$tr_houling->route}}</option>
                        </select>
                       @if($errors->has('route_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_id') }}</p>
                      @endif
                      <input type="hidden" id="route_id" name="route_id" value="{{$tr_houling->route}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_solar_type" class="col-sm-2 control-label">Solar Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="solar_type" id="solar_type">
                        <option value="">Choose Type</option>
                          @foreach($lt_solar as $item)
                            <option @if($item->id == $tr_houling->solar_type_id) selected @endif value="{{$item->id}}">{{$item->name .' - '. $item->id}}</option>
                          @endforeach
                        </select>
                      @if($errors->has('solar_type'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('solar_type') }}</p>
                      @endif
                      </div>
                    </div>
                    
                    <div class="form-group">
                      <label for="input_tonase_id" class="col-sm-2 control-label">Tonase ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-tonase-id form-control">
                          <option selected>{{$tr_houling->tonase_id}}</option>
                        </select>
                       @if($errors->has('tonase_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('tonase_id') }}</p>
                      @endif
                      <input type="hidden" id="tonase_id" name="tonase_id" value="{{ $tr_houling->tonase_id }}">
                      </div>
                    </div>

                    <input type="hidden" name="_method" id="_method" value="PUT">

                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
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

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/operational/houling_home') }}");
      }
    }

   // car select2
    $(".js-car-id").select2({
    ajax: {
      url: "{{ url('/operational/houling_home/search_car_houling') }}",
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

    $(".js-car-id").on("select2:select", function (e) {
      var obj = $(".js-car-id").select2("data")
      $('#car_id').val(obj[0].id);
      $('#car_name').val(obj[0].name);
    });
    // car select2

    // employee select2
    $(".js-employee-id").select2({
    ajax: {
      url: "{{ url('/operational/houling_home/search_employee') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using employeeom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-employee-id").on("select2:select", function (e) {
      var obj = $(".js-employee-id").select2("data")
      $('#employee_id').val(obj[0].id);
      $('#employee_name').val(obj[0].name);
    });
    // employee select2

    // route select2
    $(".js-route-id").select2({
    ajax: {
      url: "{{ url('/operational/houling_home/search_route') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using routeom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-route-id").on("select2:select", function (e) {
      var obj = $(".js-route-id").select2("data")
      $('#route_id').val(obj[0].id);
      $('#route_a').val(obj[0].route_a);
      $('#route_b').val(obj[0].route_b);
    });
    // route select2

    // tonase select2
    $(".js-tonase-id").select2({
    ajax: {
      url: "{{ url('/operational/houling_home/search_tonase') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using tonaseom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-tonase-id").on("select2:select", function (e) {
      var obj = $(".js-tonase-id").select2("data")
      $('#tonase_id').val(obj[0].id);
    });
    // tonase select2

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date()
    });

  </script>
</body>
@endsection