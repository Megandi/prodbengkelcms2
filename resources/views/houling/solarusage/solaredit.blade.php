@extends('template.app')

{{-- set title --}}
@section('title', 'Solar Edit')

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
          Solar
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Solar Home</li>
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

                <form class="form-horizontal" action="{{ url('/houling/solar_home/do_edit/'.$lt_pemakaiansolar->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_solar_id" class="col-sm-2 control-label">Solar Usage ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="solar_id" id="solar_id" placeholder="Solar Usage ID" value="{{$lt_pemakaiansolar->pemakaian_solar_id}}" readonly>
                      @if($errors->has('solar_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('solar_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_mobil_id" class="col-sm-2 control-label">ID Car <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="mobil_id" id="mobil_id" placeholder="ID Car" value="{{$lt_pemakaiansolar->mobil_id}}" readonly>
                      @if($errors->has('mobil_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('mobil_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_emp_id" class="col-sm-2 control-label">ID Employee <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="emp_id" id="emp_id" placeholder="ID Car" value="{{$lt_pemakaiansolar->karyawan_id}}" readonly>
                      @if($errors->has('emp_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('emp_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_charger" class="col-sm-2 control-label">Solar Charger <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="charger" id="charger" placeholder="Solar Charger" value="{{$lt_pemakaiansolar->nama_pengisi}}">
                      @if($errors->has('charger'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('charger') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_solar_date" class="col-sm-2 control-label">Date Usage <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                          <input type="text" class="form-control for_date" name="solar_date" id="solar_date" value="{{$lt_pemakaiansolar->tanggal_pemakaian}}">
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_nota" class="col-sm-2 control-label">Nota <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="no_nota" id="no_nota" placeholder="Nota" value="{{$lt_pemakaiansolar->no_nota}}">
                      @if($errors->has('no_nota'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_nota') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_solar_type" class="col-sm-2 control-label">Solar Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="solar_type" id="solar_type">
                        <option value="">Choose Type</option>
                          @foreach($lt_solar as $item)
                            @if( $lt_pemakaiansolar->id_solar == $item->id)
                              <option selected value="{{$lt_pemakaiansolar->id_solar}}">{{ $lt_pemakaiansolar->name_solar }}</option>
                            @else
                              <option value="{{$item->id}}">{{$item->name}}</option>
                            @endif
                          @endforeach
                        </select>
                      @if($errors->has('solar_type'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('solar_type') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_liter" class="col-sm-2 control-label">Used ( Liter ) <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="liter" id="liter" placeholder="Used ( Liter )" value="{{$lt_pemakaiansolar->liter_pemakaian_solar}}">
                      @if($errors->has('liter'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('liter') }}</p>
                      @endif
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
        window.location.replace("{{ url('/houling/solar_home') }}");
      }
    }

     $(".js-car-id").select2({
    ajax: {
      url: "{{ url('/houling/solar_home/search_car') }}",
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
      $('#own_id').val(obj[0].name);
    });

    $(".js-emp-id").select2({
    ajax: {
      url: "{{ url('/houling/solar_home/search_employee_solar') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using empom formatting functions we do not need to
        // alter the remote JSON data
          return {
            results: data
          };
        },
        cache: true
      },
      minimumInputLength: 2
    });

    $(".js-emp-id").on("select2:select", function (e) {
      var obj = $(".js-emp-id").select2("data")
      $('#emp_id').val(obj[0].id);
      $('#emp_name').val(obj[0].name);
    });

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date() 
    });

  </script>
</body>
@endsection