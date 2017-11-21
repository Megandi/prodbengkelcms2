@extends('template.app')

{{-- set title --}}
@section('title', 'Addtional Cost Add')

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
          Addtional Cost
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Addtional Cost Home</li>
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
                  <h3 class="box-title">Please complete the form before you submit.</h3>
                </div>

                <form class="form-horizontal" action="{{ url('/addtional/addtional_home/do_addnext') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}
                    <input type="hidden" value={{$validasi}} name="validasi"/>

                    <div class="form-group">
                      <label for="input_pembelian_id" class="col-sm-2 control-label">Addtional ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="hidden" name="id_biayalain" value="{{$biayalain->id}}">
                        <input type="text" required class="form-control" name="biayalain_id" id="biayalain_id" placeholder="Addtional ID" value="{{$biayalain->biayalain_id}}" readonly="true">
                      @if($errors->has('pembelian_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('biayalain_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_add_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="add_name" id="add_name" placeholder="Name" value="{{old('add_name')}}" required>
                      @if($errors->has('add_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('add_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_add_kat" class="col-sm-2 control-label">Category <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="add_kat" id="add_kat" placeholder="Category" value="{{old('add_kat')}}">
                      @if($errors->has('add_kat'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('add_kat') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_add_total" class="col-sm-2 control-label">Total <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="add_total" id="add_total" placeholder="Total" value="{{old('add_total')}}" required>
                      @if($errors->has('add_total'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('add_total') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_add_total" class="col-sm-2 control-label">Amount <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="add_mount" id="add_mount" placeholder="Amount" value="{{old('add_total')}}" required>
                      @if($errors->has('add_total'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('add_total') }}</p>
                      @endif
                      </div>
                    </div>

                  </div>
                  <div class="box-footer">
                    {{--<button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>--}}
                    <button style="width:90px;" type="submit" class="btn btn-default" onclick="close_window();return false;">Cancel</button>
                    <button style="width:90px;" type="submit" class="btn btn-info pull-right" onclick="return confirm('Are you sure you want to save this data ?')">Submit</button>
                  </div>
                </form>
              </div>

              <div class="box box-primary">
                <div class="box-header">
                {{-- disini total detail pembelian dari total semua qty --}}
                  <h3 class="box-title">Total Qty : {{$total}} |</h3>
                  <h3 class="box-title">Total Amount : {{number_format($totalharga)}}</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Sub Price</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($biayalain->detail->where('status', 'A') as $detail)
                      <tr>
                        <td>{{$detail->nama}}</td>
                        <td>{{$detail->kategori}}</td>
                        <td>{{$detail->jumlah}}</td>
                        <td>{{number_format($detail->harga)}}</td>
                        <td><a style="width:90px;" href="{{ url('/addtional/addtional_home/delete_addtional_detail/'.$detail->id.'/'.$biayalain->id)}}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>
                      </tr>
                     @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Sub Price</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>

                <div class="box-footer">
                  <a style="width:90px;" href="{{ url('/addtional/addtional_home/checkout/'.$biayalain->id) }}" type="button" class="btn btn-success pull-right">Checkout</a>
                </div>

              </div>
            </div>
        </div>
      </div>
    </section>

	</div>

	<script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/addtional/addtional_home') }}");
      }
    }

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD',
      maxDate: new Date()
    });

	</script>
</body>
@endsection
