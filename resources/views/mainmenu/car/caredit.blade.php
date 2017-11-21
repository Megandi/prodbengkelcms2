@extends('template.app')

{{-- set title --}}
@section('title', 'Car Edit')

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
          Car
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Car Home</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/car_home/do_edit/'.$ms_mobil->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_car_id" class="col-sm-2 control-label">Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="type" id="type" placeholder="Car ID" value="@if($ms_mobil->type_car == 1){{'Office'}}@else{{'Customer'}}@endif" readonly>
                      @if($errors->has('type'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('type') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_id" class="col-sm-2 control-label">Car ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_id" id="car_id" placeholder="Car ID" value="{{$ms_mobil->mobil_id}}" readonly>
                      @if($errors->has('car_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_id" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="cust_id" id="cust_id" placeholder="Car ID" value="@if($ms_mobil->type_car == 1){{ $ms_mobil->name_employee }}@else{{ $ms_mobil->name_customer }}@endif" readonly>
                      @if($errors->has('cust_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('cust_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_nopol" class="col-sm-2 control-label">No Policy <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_nopol" id="car_nopol" placeholder="No Policy" value="{{$ms_mobil->no_polisi_mobil}}">
                      @if($errors->has('car_nopol'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_nopol') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_address" class="col-sm-2 control-label">Address <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control area" rows="3" name="car_address" id="car_address" placeholder="Address">{{$ms_mobil->alamat_pemilik}}</textarea>
                      @if($errors->has('car_address'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_address') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_brand" class="col-sm-2 control-label">Brand <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_brand" id="car_brand" placeholder="Brand" value="{{$ms_mobil->merek_mobil}}">
                      @if($errors->has('car_brand'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_brand') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_type" class="col-sm-2 control-label">Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_type" id="car_type" placeholder="Type" value="{{$ms_mobil->tipe_mobil}}">
                      @if($errors->has('car_type'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_type') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_group" class="col-sm-2 control-label">Group <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_group" id="car_group" placeholder="Group" value="{{$ms_mobil->jenis_mobil}}">
                      @if($errors->has('car_group'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_group') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_model" class="col-sm-2 control-label">Model <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_model" id="car_model" placeholder="Model" value="{{$ms_mobil->model}}">
                      @if($errors->has('car_model'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_model') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_prod" class="col-sm-2 control-label">Production Year <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="car_prod" id="car_prod" placeholder="Production Year" value="{{$ms_mobil->tahun_pembuatan_mobil}}">
                      @if($errors->has('car_prod'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_prod') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_car_color" class="col-sm-2 control-label">Color <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="car_color" id="car_color" placeholder="Color" value="{{$ms_mobil->warna_mobil}}">
                      @if($errors->has('car_color'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('car_color') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_chassis" class="col-sm-2 control-label">Chassis Number <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="no_chassis" id="no_chassis" placeholder="Chassis Number" value="{{$ms_mobil->no_rangka_mobil}}">
                      @if($errors->has('no_chassis'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_chassis') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_silinder" class="col-sm-2 control-label">Cylinder <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="silinder" id="silinder" placeholder="Cylinder" value="{{$ms_mobil->isi_silinder_mobil}}">
                      @if($errors->has('silinder'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('silinder') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_fuel" class="col-sm-2 control-label">Fuel <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="fuel" id="fuel" placeholder="Fuel" value="{{$ms_mobil->bahan_bakar_mobil}}"">
                      @if($errors->has('fuel'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('fuel') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_bpkb" class="col-sm-2 control-label">BPKB <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="bpkb" id="bpkb" placeholder="BPKB" value="{{$ms_mobil->no_bpkb_mobil}}">
                      @if($errors->has('bpkb'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('bpkb') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_reg_date" class="col-sm-2 control-label">Registration Year <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="reg_date" id="reg_date" placeholder="Registration Date" value="{{$ms_mobil->tahun_registrasi_mobil}}">
                      @if($errors->has('reg_date'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('reg_date') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_indent" class="col-sm-2 control-label">Indent <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="indent" id="indent" placeholder="Indent" value="{{$ms_mobil->indent_mobil}}">
                      @if($errors->has('indent'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('indent') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_status_car" class="col-sm-2 control-label">Status Car <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="status_car" id="status_car" placeholder="Status Car" value="{{$ms_mobil->status_mobil}}">
                      @if($errors->has('status_car'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('status_car') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_machine_car" class="col-sm-2 control-label">Machine Number <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="machine_car" id="machine_car" placeholder="Machine number" value="{{$ms_mobil->no_mesin_mobil}}">
                      @if($errors->has('machine_car'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('machine_car') }}</p>
                      @endif
                      </div>
                    </div>

                    <input type="hidden" name="_method" id="_method" value="PUT">

                  </div>
                  <div class="box-footer">
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this data ?')">Submit</button>
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
        window.location.replace("{{ url('/mainmenu/car_home') }}");
      }
    }

    $(".js-cust-id").select2({
    ajax: {
      url: "{{ url('/mainmenu/car_home/search_cust') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term, // search term
          j: $('#type').val() // search term
        };
      },
      processResults: function (data) {
        // parse the results into the format expected by Select2.
        // since we are using custom formatting functions we do not need to
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

  </script>
</body>
@endsection