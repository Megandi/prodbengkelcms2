@extends('template.app')

{{-- set title --}}
@section('title', 'Selling Return Add')

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
          Selling Return
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Selling Return Home</li>
          <li class="active">Add</li>
          <li class="active">Cut The Debt</li>
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

              <div class="box box-primary">
                <div class="box-header">
                  <div class="col-sm-9">
                    Customer ID : {{$customer_id}}
                  </div>
                  <div class="col-sm-3">
                    Total Return : Rp {{number_format($total, '2')}}
                  </div>
                </div>
                <form action="{{url('finance/salreturn_home/addnext/docdb')}}" role="form" method="POST" onsubmit="return validasitrue();">
                  <input type="hidden" name="customer_id" value="{{$customer_id}}"/>
                  <input type="hidden" name="total_return" value="{{$total}}"/>
                  <input type="hidden" name="retur_id" value="{{$retur_id}}"/>
                  {{ csrf_field() }}
                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Debt ID</th>
                        <th>Total</th>
                        <th>Debt Total</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php $totalhutang = 0; ?>
                      @foreach($lt_piutang as $hutang)
                      <tr>
                        <td>{{$hutang->piutang_id}}</td>
                        <td>{{number_format($hutang->total)}}</td>
                        <td>{{number_format($hutang->total - $hutang->bayar)}}</td>
                        <?php $totalhutang += $hutang->total - $hutang->bayar;?>
                        <td>
                          <input type="checkbox" class="messageCheckbox" onclick="testlagi();" name="cbpilih[{{$hutang->id}}]" value="{{$hutang->total - $hutang->bayar}}"/>
                        </td>
                      </tr>
                     @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Debt ID</th>
                        <th>Total</th>
                        <th>Debt Total</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
                <div class="box-footer">
                  <div class="col-sm-4">
                  </div>
                  <div class="col-sm-3">
                    <b><font id="totaldebt" class="pull-right">0.00&nbsp;&nbsp;&nbsp;</font></b>
                    <font class="pull-right">Total Debt : &nbsp;</font>
                  </div>
                  <div class="col-sm-3">
                    <b><font id="totalcash" class="pull-right">0.00 </font>&nbsp;&nbsp;&nbsp;&nbsp;</b>
                    <font class="pull-right">Total Cash can return : &nbsp;</font>
                  </div>
                  <div class="col-sm-2">
                    <a onclick="validasi();" class="btn btn-primary pull-right">Cut The Debt</a>
                  </div>
                </div>

              <div class="modal fade" id="rejectModal1" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                      <h4 class="modal-title" style="color:red">Are you sure want to continue?</h4>
                    </div>
                  <div class="modal-body">
                    All Your Total Debt <b><font id="alltotaldebt" class="pull-right">{{number_format($totalhutang)}}</font></b><hr>
                    Total Debt will be you pay <b><font id="totaldebt2" class="pull-right">0.00&nbsp;&nbsp;&nbsp;</font></b><hr>
                    Balance <b><font id="balance" class="pull-right">0.00&nbsp;&nbsp;&nbsp;</font></b><hr>
                    Total Cash will be return <b><font id="totalcash2" class="pull-right">0.00&nbsp;&nbsp;&nbsp;</font></b>
                  </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Continue</button>
                    </div>
                  </div>
                </div>
              </div>
              </form>
              </div>
            </div>
        </div>
      </div>
    </section>
  </div>
</body>

<div class="modal fade" id="rejectModal0" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" style="color:red">Failed</h4>
      </div>
	  <div class="modal-body">
		  <p>Failed! Total Debt is more than Total Cash Return.</p>
	  </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Oke</button>
      </div>
    </div>
  </div>
</div>

<script>
    var totalreturn =  <?php echo json_encode($total) ?>;
    var checkedValue = 0;
    $("#totalcash").html(totalreturn);
    function testlagi(){
      checkedValue = 0;
      var inputElements = document.getElementsByClassName('messageCheckbox');
      for(var i=0; inputElements[i]; ++i){
          if(inputElements[i].checked){
               checkedValue += parseInt(inputElements[i].value);
          }
      }
      $("#totaldebt").html(checkedValue);
      if(totalreturn-checkedValue<0){
        $("#totalcash").html(0);
      } else {
        $("#totalcash").html(totalreturn-checkedValue);
      }
    }

    // function validasitrue(){
    //     if(totalreturn<checkedValue){
    //         $('#rejectModal0').modal();
    //         return false;
    //     }
    // }

    function validasi(){
      if(totalreturn<checkedValue){
        $('#rejectModal1').modal();

        var totalcashh = 0;
        var totalcash2 = totalcashh.toFixed(2).replace(/./g, function(c, i, a) {
              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });

        $("#totaldebt2").html(totalreturn);
        $("#totalcash2").html(totalcash2);

      } else {
        $('#rejectModal1').modal();

        var totaldebt2 = checkedValue.toFixed(2).replace(/./g, function(c, i, a) {
              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });

        var totalcashh = totalreturn-checkedValue;
        var totalcash2 = totalcashh.toFixed(2).replace(/./g, function(c, i, a) {
              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });

        balance = totalreturn - checkedValue;
        var balance2 = balance.toFixed(2).replace(/./g, function(c, i, a) {
              return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
        });

        $("#totaldebt2").html(totaldebt2);
        $("#totalcash2").html(totalcash2);
        $("#balance").html(balance2);
      }

    }

</script>

@endsection
