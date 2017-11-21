@extends('template.app')

{{-- set title --}}
@section('title', 'Tonase Edit')

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
          Tonase
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Tonase Home</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/tonase_home/do_edit/'.$tr_tonase->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_id_tonase" class="col-sm-2 control-label">Tonase ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="id_tonase" id="id_tonase" placeholder="Tonase ID" value="{{$tr_tonase->id_tonase}}" readonly>
                      @if($errors->has('id_tonase'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('id_tonase') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_route_id" class="col-sm-2 control-label">Route ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-route-id form-control">
                          @if(isset($tr_tonase->id_route))
                            <option selected>{{$tr_tonase->id_route}}</option>
                          @endif
                        </select>
                       @if($errors->has('route_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('route_id') }}</p>
                      @endif
                          <input type="hidden" id="route_id" name="route_id" value="{{$tr_tonase->id_route}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_tonase_p_a" class="col-sm-2 control-label">Tonase Percent A (%) <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" max="100" class="form-control" name="tonase_p_a" id="tonase_p_a" placeholder="Tonase Percent A" value="{{$tr_tonase->tonase_percent_a}}">
                      @if($errors->has('tonase_p_a'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('tonase_p_a') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_tonase_n_a" class="col-sm-2 control-label">Tonase Number A <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="tonase_n_a" id="tonase_n_a" placeholder="Tonase Number A" value="{{$tr_tonase->tonase_a}}">
                      @if($errors->has('tonase_n_a'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('tonase_n_a') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_tonase_p_b" class="col-sm-2 control-label">Tonase Percent B (%) <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" max="100" class="form-control" name="tonase_p_b" id="tonase_p_b" placeholder="Tonase Percent B" value="{{$tr_tonase->tonase_percent_b}}">
                      @if($errors->has('tonase_p_b'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('tonase_p_b') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_tonase_n_b" class="col-sm-2 control-label">Tonase Number B <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="tonase_n_b" id="tonase_n_b" placeholder="Tonase Number B" value="{{$tr_tonase->tonase_b}}">
                      @if($errors->has('tonase_n_b'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('tonase_n_b') }}</p>
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
        window.location.replace("{{ url('/mainmenu/tonase_home') }}");
      }
    }

    // route select2
    $(".js-route-id").select2({
    ajax: {
      url: "{{ url('/mainmenu/tonase_home/search_tonase_route') }}",
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
      $('#route_id_a').val(obj[0].route_a);
      $('#route_id_b').val(obj[0].route_b);
    });
    // route select2
	</script>
</body>
@endsection