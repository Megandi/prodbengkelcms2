@extends('template.app')

{{-- set title --}}
@section('title', 'Salary Add')

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
          Salary
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Salary Home</li>
          <li class="active">Add</li>
        </ol>
      </section>

      <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-info">
                <div class="box-header with-border">
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/hrd/sal_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_karyawan_id" class="col-sm-2 control-label">Employee Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-karyawan-id form-control">
                          <option selected>{{old('karyawan_id')}} - {{old('karyawan_name')}}</option>
                        </select>
                       @if($errors->has('karyawan_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('karyawan_id') }}</p>
                      @endif
                      <input type="hidden" id="karyawan_id" name="karyawan_id" value="{{old('karyawan_id')}}">
                      <input type="hidden" id="karyawan_name" name="karyawan_name" value="{{old('karyawan_name')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_salary" class="col-sm-2 control-label">Amount Salary <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="salary" id="salary" placeholder="Amount Salary" value="{{old('salary')}}">
                      @if($errors->has('salary'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('salary') }}</p>
                      @endif
                      </div>
                    </div>

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
        window.location.replace("{{ url('/hrd/sal_home') }}");
      }
    }

    $(".js-karyawan-id").select2({
    ajax: {
      url: "{{ url('/hrd/sal_home/search_employee') }}",
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return {
          q: params.term // search term
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

    $(".js-karyawan-id").on("select2:select", function (e) {
      var obj = $(".js-karyawan-id").select2("data")
      $('#karyawan_id').val(obj[0].id);
      $('#karyawan_name').val(obj[0].name);
    });

  </script>
</body>
@endsection