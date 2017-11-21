@extends('template.app')

{{-- set title --}}
@section('title', 'Loan Edit')

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
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Loan Home</li>
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

                <form class="form-horizontal" action="{{ url('/finance/loan_home/do_edit/'.$lt_loan->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_loan_id" class="col-sm-2 control-label">Loan ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="loan_id" id="loan_id" placeholder="Loan ID" value="{{$lt_loan->loan_id}}" readonly>
                      @if($errors->has('loan_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('loan_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_user_id" class="col-sm-2 control-label">User ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="user_id" id="user_id" placeholder="User ID" value="@if(substr($lt_loan->user_id,0,1) == 'E'){{ $lt_loan->employee_name .' - '. $lt_loan->user_id }}
                        @elseif(substr($lt_loan->user_id,0,1) == 'C'){{ $lt_loan->customer_name .' - '. $lt_loan->user_id }}
                        @elseif(substr($lt_loan->user_id,0,1) == 'S'){{ $lt_loan->supplier_name .' - '. $lt_loan->user_id }}
                        @elseif(substr($lt_loan->user_id,0,1) == 'A'){{ $lt_loan->user_id }}@endif" readonly>
                      @if($errors->has('user_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('user_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_grand_total" class="col-sm-2 control-label">Grand Total <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="grand_total" id="grand_total" placeholder="Grand Total" value="Rp {{number_format($lt_loan->total)}}" readonly>
                        <input type="hidden" name="total" id="total" value="{{$lt_loan->total}}">
                      @if($errors->has('grand_total'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('grand_total') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_already_paid" class="col-sm-2 control-label">Loan Already Paid <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="already_paid_v" id="already_paid_v" placeholder="Loan Already Paid" value="Rp {{number_format($lt_loan->bayar)}}" readonly>
                        <input type="hidden" name="already_paid" id="already_paid" value="{{$lt_loan->bayar}}">
                      @if($errors->has('already_paid'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('already_paid') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_due_date" class="col-sm-2 control-label">Due Date <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker'>
                          <input type="text" class="form-control for_date" name="due_date" id="due_date" value="{{ date("d/m/Y",strtotime($lt_loan->tanggal_jatuh_tempo)) }}" readonly>
                          @if($errors->has('due_date'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('due_date') }}</p>
                          @endif
                        </div>
                      </div>
                    </div>

                    @if($lt_loan->total > $lt_loan->bayar)
                      <div class="form-group">
                        <label for="input_payable" class="col-sm-2 control-label">Total Loan payable <span style="color:red;">*</span></label>
                        <div class="col-sm-10">
                          <input type="number" max="{{$lt_loan->total - $lt_loan->bayar}}" class="form-control" name="payable" id="payable" placeholder="Total Loan payable" value="{{$lt_loan->total - $lt_loan->bayar}}">
                        @if($errors->has('payable'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('payable') }}</p>
                        @endif
                        </div>
                      </div>
                    @else
                      <div class="form-group">
                        <label for="input_payable" class="col-sm-2 control-label">Total Loan payable <span style="color:red;">*</span></label>
                        <div class="col-sm-10">
                          <input type="number" max="{{$lt_loan->total - $lt_loan->bayar}}" class="form-control" name="payable" id="payable" placeholder="Total Loan payable" value="{{$lt_loan->total - $lt_loan->bayar}}" readonly>
                        @if($errors->has('payable'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('payable') }}</p>
                        @endif
                        </div>
                      </div>
                    @endif
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

  </div>

  <script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/finance/loan_home') }}");
      }
    }

  </script>
</body>
@endsection
