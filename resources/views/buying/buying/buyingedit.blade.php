@extends('template.app')

{{-- set title --}}
@section('title', 'Buying Edit')

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
          Buying
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Buying Home</li>
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

                <form class="form-horizontal" action="{{ url('/buying/buying_home/do_edit/'.$buying->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_pembelian_id" class="col-sm-2 control-label">Buying ID <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" required class="form-control" name="pembelian_id_view" id="pembelian_id_view" placeholder="Buying ID" value="{{ $buying->pembelian_id }}" readonly>
                      @if($errors->has('pembelian_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('pembelian_id') }}</p>
                      @endif
                      <input type="hidden" id="pembelian_id" name="pembelian_id" value="{{$buying->pembelian_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_id" class="col-sm-2 control-label">Supplier <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" required class="form-control" name="items_id_view" id="items_id_view" placeholder="Supplier" value="{{ $buying->supplier['nama']}}" readonly>
                      @if($errors->has('items_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_id') }}</p>
                      @endif
                      <input type="hidden" id="items_id" name="items_id" value="{{$buying->supplier_id}}">
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_grand_total_view" class="col-sm-2 control-label">Grand Total <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="text" required class="form-control" name="items_grand_total_view" id="items_grand_total_view" placeholder="Grand Total" value="{{ number_format($buying->pembelian_total) }}" readonly>
                      @if($errors->has('items_grand_total_view'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_grand_total_view') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_no_nota" class="col-sm-2 control-label">No Nota <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="no_nota" id="no_nota" placeholder="No Nota" value="{{ $buying['no_nota'] }}" required>
                      @if($errors->has('no_nota'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('no_nota') }}</p>
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

              <div class="box box-primary">
                <div class="box-header">
                {{-- disini total detail pembelian dari total semua qty --}}
                  <h3 class="box-title">Cart - Grand Total : Rp {{number_format($total)}}</h3>
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>Items</th>
                        <th>Quantities</th>
                        <th>Sub Price</th>
                        <th>Total Price</th>
                        {{--<th>Action</th>--}}
                      </tr>
                    </thead>
                    <tbody>

                      @foreach($buying->detail as $detail)
                      <tr>
                        @if($detail->status == "T")
                          <td>{{$detail->barang_temp->nama}}</td>
                        @else
                          <td>{{$detail->barang->nama}}</td>
                        @endif
                        <td>{{$detail->qty}}</td>
                        <td>{{number_format($detail->sub_total_pembelian)}}</td>
                        <td>{{number_format($detail->sub_total_pembelian * $detail->qty)}}</td>
                        {{--<td><a style="width:90px;" href="{{ url('/buying/buying_home/delete_buying_detail2/'.$detail->id.'/'.$buying->id)}}" type="button" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this data ?')">Delete</a></td>--}}
                      </tr>
                     @endforeach

                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID Items</th>
                        <th>Quantities</th>
                        <th>Sub Price</th>
                        <th>Total Price</th>
                        {{--<th>Action</th>--}}
                      </tr>
                    </tfoot>
                  </table>
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
        window.location.replace("{{ url('/buying/buying_home') }}");
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
                  title: 'TUNAS ABADI 8 | Purchase List',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | Purchase List',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | Purchase List',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | Purchase List',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed two-column',
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
