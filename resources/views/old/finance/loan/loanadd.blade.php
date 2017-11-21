@extends('template.app')

{{-- set title --}}
@section('title', 'Loan Add')

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
          Loan
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Loan Home</li>
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

                <form class="form-horizontal" action="{{ url('/finance/loan_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_loan_type" class="col-sm-2 control-label">Type <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="type" id="type" required>
                          <option value="">Choose Type</option>
                          <option @if(old('type') == '1') selected @endif value="1">Employee</option>
                          <option @if(old('type') == '2') selected @endif value="2">Customer</option>
                          <option @if(old('type') == '3') selected @endif value="3">Supplier</option>
                        </select>
                      @if($errors->has('type'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('type') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_user_id" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select style="text-transform:uppercase" class="js-user-id form-control">
                          <option selected>{{old('user_id')}} - {{old('user_name')}}</option>
                        </select>
                       @if($errors->has('user_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('user_id') }}</p>
                      @endif
                      <input type="hidden" id="user_id" name="user_id" value="{{old('user_id')}}">
                      <input type="hidden" id="user_name" name="user_name" value="{{old('user_name')}}">
                      <input type="hidden" id="user_total" name="user_total" value="{{old('user_total')}}">
                      <input type="hidden" id="loan_id" name="loan_id" value="{{old('loan_id')}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_total_loan" class="col-sm-2 control-label">Total Loan <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" required class="form-control" name="total_loan" id="total_loan" placeholder="Total Loan" value="{{old('total_loan')}}">
                      @if($errors->has('total_loan'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('total_loan') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_due_date" class="col-sm-2 control-label">Due Date <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                          <input type="text" required class="form-control for_date" name="due_date" id="due_date" value="{{old('due_date')}}">
                          @if($errors->has('due_date'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('due_date') }}</p>
                          @endif
                        </div>
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

  </div>

  <script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/finance/loan_home') }}");
      }
    }

    $(".js-user-id").select2({
    ajax: {
      url: "{{ url('/finance/loan_home/search_loan_user') }}",
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
        // since we are using userom formatting functions we do not need to
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
      $('#user_total').val(obj[0].total);
      $('#loan_id').val(obj[0].loan_id);
    });

    // for datetimepicker
    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      minDate: new Date()
    });


  </script>
</body>
@endsection
