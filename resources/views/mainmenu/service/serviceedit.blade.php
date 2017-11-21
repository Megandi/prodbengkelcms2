@extends('template.app')

{{-- set title --}}
@section('title', 'Service Edit')

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
          Service
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Service Home</li>
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

                <form class="form-horizontal" action="{{ url('/mainmenu/service_home/do_edit/'.$ms_jasa->id.'/'.$ms_jasa->barang_id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                     <div class="form-group">
                      <label for="input_service_id" class="col-sm-2 control-label">Service ID <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="service_id" id="service_id" placeholder="Service ID" value="{{$ms_jasa->service_id}}" readonly>
                      @if($errors->has('service_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('service_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="name" id="name" placeholder="Name" value="{{$ms_jasa->name}}">
                      @if($errors->has('name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items" class="col-sm-2 control-label">Items <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="items_view" id="items_view" placeholder="Items" value="{{$ms_jasa->barang_id}} - {{$ms_jasa->barang_name}}" readonly>
                      @if($errors->has('items_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_price" class="col-sm-2 control-label">Price <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="price_view" id="price_view" placeholder="price" value="{{$ms_jasa->price}}" readonly>
                      @if($errors->has('price_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('price_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_qty" class="col-sm-2 control-label">Quantities <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="qty" id="qty" placeholder="qty" value="{{$ms_jasa->qty}}" readonly>
                      @if($errors->has('qty'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('qty') }}</p>
                      @endif
                      </div>
                    </div>

                    <input type="hidden" name="status_call" id="status_call" value="{{$ms_jasa->status}}">
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
        window.location.replace("{{ url('/mainmenu/service_home') }}");
      }
    }

    // items select2
    $(".js-items-id").select2({
    ajax: {
      url: "{{ url('/mainmenu/service_home/search_items_service') }}",
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
      $('#items_id_barang').val(obj[0].id_barang);
      $('#items_qty').val(obj[0].qty);
      $('#items_name').val(obj[0].name);
      $('#qty').val(obj[0].qty);
      $('#price').val(obj[0].sub_total);
    });
    // items select2

	</script>
</body>
@endsection