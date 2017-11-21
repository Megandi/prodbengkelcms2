@extends('template.app')

{{-- set title --}}
@section('title', 'Addtional Edit')

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
          Addtional
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Addtional Home</li>
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

                <form class="form-horizontal" action="{{ url('/finance/addtional_home/do_edit/'.$lt_biayalain->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_sell_nota" class="col-sm-2 control-label">Addtional ID</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="no_nota" id="sell_nota" placeholder="Nota" value="{{$lt_biayalain->biayalain_id}}" readonly>
                      @if($errors->has('sell_nota'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('sell_nota') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_buy_date" class="col-sm-2 control-label">Date <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <div class='input-group date' id='datetimepicker1'>
                          <input type="text" class="form-control for_date" value="<?= date("d/m/Y",strtotime($lt_biayalain->tanggal));?>" name="buy_date" id="buy_date" readonly>
                        </div>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_sell_dp" class="col-sm-2 control-label">Amount Cost <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" class="form-control" name="sell_dp" id="sell_dp" placeholder="Down Payment" value="{{$lt_biayalain->total_biaya}}" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_status_payment" class="col-sm-2 control-label">Type Addtional <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control for_date" @if($lt_biayalain->type_cost==1) value="Already" @else value="Not Yet" @endif name="buy_date" id="buy_date" readonly>
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_sell_nota" class="col-sm-2 control-label">No Nota <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" required class="form-control" name="no_nota" id="no_nota" placeholder="Nota" value="{{$lt_biayalain->no_nota}}">
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

              <div class="box box-primary">
                <div class="box-header">
                {{-- disini total detail pembelian dari total semua qty --}}
                  <h3 class="box-title">Grand Total : Rp {{number_format($lt_biayalain->total_biaya * $lt_biayalain->detail->SUM('jumlah'))}}</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Sub Price</th>
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($lt_biayalain->detail->where('status', 'A') as $detail)
                      <tr>
                        <td>{{$detail->nama}}</td>
                        <td>{{$detail->kategori}}</td>
                        <td>{{$detail->jumlah}}</td>
                        <td>{{number_format($detail->harga)}}</td>
                      </tr>
                     @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Sub Price</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>

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
        window.location.replace("{{ url('/finance/addtional_home') }}");
      }
    }

    $(function () {
        $('#table-home tfoot th').each( function () {
          var title = $(this).text();
          $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
        });

        var table = $('#table-home').DataTable({
          responsive: true,
          stateSave: true,
          "paging": true,
          "lengthChange": true,
          "ordering": true,
          "info": true,
          "autoWidth": true,
          "order": [[ 0, "desc" ]],
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
          // dom: 'lrtipB',
          // buttons: [
          //         'copy', 'csv', 'excel', 'pdf', 'print'
          // ]
          dom: 'Bfrtip',
          buttons: [
              {
                  extend: 'copyHtml5',
                  title: 'TUNAS ABADI 8 | List Addtional Cost',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | List Addtional Cost',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | List Addtional Cost',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | List Addtional Cost',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed four-column',
                columnText: function ( dt, idx, title ) {
                    return (idx+1)+': '+title;
                }
              },
          ],
            columnDefs: [ {
                targets: -1,
                visible: false
            }]
        });

        // Apply the search
        table.columns().every( function () {
          var that = this;
          $( 'input', this.footer() ).on( 'keyup change', function () {
            if ( that.search() !== this.value ) {
              that
              .search( this.value )
              .draw();
            }
          });
        });
    });

  </script>
</body>
@endsection
