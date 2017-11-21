@extends('template.app')

{{-- set title --}}
@section('title', 'Route Edit')

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
          Route
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Route Home</li>
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

                <form class="form-horizontal" action="{{ url('/operational/route_home/do_edit/'.$lt_route->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_route_id" class="col-sm-2 control-label">Route ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="route_id" id="route_id" placeholder="route_id" value="{{$lt_route->route_id}}" readonly>
                      @if($errors->has('route_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_type_a" class="col-sm-2 control-label">Route Type A <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="route_type_a" id="route_type_a">
                          <option value="">Choose Type</option>
                          <option @if($lt_route->route_type_a == '1') selected @endif value="1">Quarry</option>
                          <option @if($lt_route->route_type_a == '2') selected @endif value="2">Port</option>
                          <option @if($lt_route->route_type_a == '3') selected @endif value="3">Customer</option>
                        </select>
                      @if($errors->has('route_type_a'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_type_a') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_a" class="col-sm-2 control-label">Name of Route A <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-route_a form-control">
                          @if(isset($lt_route->route_a))
                            <option selected>{{$lt_route->route_a}}</option>
                          @endif
                        </select>
                       @if($errors->has('route_a'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_a') }}</p>
                      @endif
                      <input type="hidden" id="route_a" name="route_a" value="{{$lt_route->route_a}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_type_b" class="col-sm-2 control-label">Route Type B <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="route_type_b" id="route_type_b">
                          <option value="">Choose Type</option>
                          <option @if($lt_route->route_type_b == '1') selected @endif value="1">Quarry</option>
                          <option @if($lt_route->route_type_b == '2') selected @endif value="2">Port</option>
                          <option @if($lt_route->route_type_b == '3') selected @endif value="3">Customer</option>
                        </select>
                      @if($errors->has('route_type_b'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_type_b') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_b" class="col-sm-2 control-label">Name of Route B <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-route_b form-control">
                          @if(isset($lt_route->route_b))
                            <option selected>{{$lt_route->route_b}}</option>
                          @endif
                        </select>
                       @if($errors->has('route_b'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_b') }}</p>
                      @endif
                      <input type="hidden" id="route_b" name="route_b" value="{{$lt_route->route_b}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_distance" class="col-sm-2 control-label">Distance (Km) <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="distance" id="distance" placeholder="Distance (Km)" value="{{$lt_route->distance}}">
                      @if($errors->has('distance'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('distance') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_hour" class="col-sm-2 control-label">Hour <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="hour" id="hour" placeholder="Hour" value="{{$lt_route->hour}}">
                      @if($errors->has('hour'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('hour') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_minute" class="col-sm-2 control-label">Minutes <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="minute" id="minute" placeholder="Minutes" value="{{$lt_route->minute}}">
                      @if($errors->has('minute'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('minute') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_second" class="col-sm-2 control-label">Seconds <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="second" id="second" placeholder="Seconds" value="{{$lt_route->second}}">
                      @if($errors->has('second'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('second') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_liter" class="col-sm-2 control-label">Liter <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="liter" id="liter" placeholder="Liter" value="{{$lt_route->liter}}">
                      @if($errors->has('liter'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('liter') }}</p>
                      @endif
                      </div>
                    </div>
                    @if(Auth::user()->level_id == 0 || Auth::user()->level_id == 1)
                    <div class="form-group">
                      <label for="input_komisi" class="col-sm-2 control-label">Commission <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="komisi" id="komisi" placeholder="Commission" value="{{$lt_route->komisi}}">
                      @if($errors->has('komisi'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('komisi') }}</p>
                      @endif
                      </div>
                    </div>
                    @endif
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
        window.location.replace("{{ url('/operational/route_home') }}");
      }
    }

    $(".js-route_a").select2({
    ajax: {
      url: "{{ url('/operational/route_home/search_route_a') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term, // search term
          j: $('#route_type_a').val() // search term
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

    $(".js-route_a").on("select2:select", function (e) {
      var obj = $(".js-route_a").select2("data")
      $('#route_a').val(obj[0].id);
      $('#route_a_name').val(obj[0].name);
    });

    $(".js-route_b").select2({
    ajax: {
      url: "{{ url('/operational/route_home/search_route_b') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term, // search term
          j: $('#route_type_b').val() // search term
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

    $(".js-route_b").on("select2:select", function (e) {
      var obj = $(".js-route_b").select2("data")
      $('#route_b').val(obj[0].id);
      $('#route_b_name').val(obj[0].name);
    });

	</script>
</body>
@endsection