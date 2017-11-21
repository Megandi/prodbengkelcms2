@extends('template.app')

{{-- set title --}}
@section('title', 'Sales Return Add')

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
          Sales Return
          <small>Add</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Sales Return Home</li>
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
                  <form class="form-horizontal" action="{{ url('/sales/salreturn_home/do_add') }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_return_id" class="col-sm-2 control-label">ID Sales Return</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="return_id" id="return_id" placeholder="ID Sales Return" value="{{$return->retur_id}}" readonly="true">
                      @if($errors->has('return_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('return_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_return_id" class="col-sm-2 control-label">ID Selling</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="return_id" id="return_id" placeholder="ID Sales Return" value="{{$return->penjualan->penjualan_id}}" readonly="true">
                      @if($errors->has('return_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('return_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_return_id" class="col-sm-2 control-label">Customer</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" value="{{$return->penjualan->customer_id}} - {{$return->penjualan->customer->nama}}" readonly="true">
                          @if($errors->has('return_id'))
                            <p style="font-style: bold; color: red;">{{ $errors->first('return_id') }}</p>
                          @endif
                      </div>
                    </div>
                  </div>
                </form>
              </div>
              <div class="box box-primary">
                <div class="box-header">
                  List Items in Selling : {{$return->penjualan->penjualan_id}}
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>ID Items</th>
                        <th>Items</th>
                        <th>Quantities</th>
                        <th>Quantities Return</th>
                        <th>Sub Total</th>
                        <th>Sub Total</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($penjualan->detail as $detail)
                      <tr>
                        <td>{{$detail->barang_id}}</td>
                        @if($detail->status == "T")
                          <td>{{$detail->barang_temp->nama}} <i>(temp)</i></td>
                        @else
                          @if($detail->type_sell==1)
                            <td>{{$detail->jasa->name}}</td>
                          @else
                            <td>{{$detail->barang->nama}}</td>
                          @endif
                        @endif
                        <td>{{$detail->qty}}</td>
                        <td>{{$detail->qty_return}}</td>
                        <td>{{number_format($detail->sub_total_penjualan)}}</td>
                        <td>{{number_format($detail->qty*$detail->sub_total_penjualan)}}</td>
                        @if($detail->qty-$detail->qty_return>0 && $detail->status == "A")
                          <td><a style="width:90px;" type="button" onclick="barang({{$detail->id}})" class="btn btn-primary">Return</a></td>
                        @else
                          <td><a style="width:90px;" href="" type="button" class="btn btn-default disabled">Return</a></td>
                        @endif
                      </tr>
                     @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID Items</th>
                        <th>Items</th>
                        <th>Quantities</th>
                        <th>Quantities Return</th>
                        <th>Sub Price</th>
                        <th>Sub Total</th>
                        <th>Action</th>
                      </tr>
                    </tfoot>
                  </table>
                </div>
              </div>
              <div class="box box-primary">
                <div class="box-header">
                  List Items in Sales Return : {{$return->retur_id}}
                </div>

                <div class="box-body table-responsive">
                  <table id="table-home1" class="table table-bordered table-hover">
                    <thead>
                      <tr>
                        <th>ID Items</th>
                        <th>Items</th>
                        <th>Quantities</th>
                        <th>Sub Price</th>
                        <th>Sub Total</th>
                        <th>Type</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody>
                      @foreach($return->detail as $detail)
                      <tr>
                        <td>{{$detail->barang_id}}</td>
                        @if($detail->status == "T")
                          <td>{{$detail->barang_temp->nama}} <i>(temp)</i></td>
                        @else
                          @if($detail->type_sell==1)
                            <td>{{$detail->jasa->name}}</td>
                          @else
                            <td>{{$detail->barang->nama}}</td>
                          @endif
                        @endif
                        <td>{{$detail->qty}}</td>
                        <td>{{number_format($detail->sub_total)}}</td>
                        <td>{{number_format($detail->qty*$detail->sub_total)}}</td>
                        <td>@if($detail->type_return == 1){{'Cut the debt'}}@elseif($detail->type_return == 2){{'Change Money'}}@elseif($detail->type_return == 3){{'Replace items at the same price'}}@elseif($detail->type_return == 4){{'Trade-in Goods'}}@endif</td>
                        @if($detail->type_return == 4 )
                          <?php $penjualan = App\Models\manage_selling::where('returdetail_id', $detail->id)->first();?>
                          @if(!$penjualan)
                            <!-- add  -->
                            <td><a style="width:90px;" href="{{url('selling/selling_home/tig/add/'.$detail->qty*$detail->sub_total.'/'.$return->id.'/'.$detail->id)}}" type="button" class="btn btn-warning">Continue</a></td>
                          @elseif($penjualan->no_nota=="")
                            <!-- add next -->
                            <td><a style="width:90px;" href="{{url('selling/selling_home/tig/addnext/'.$penjualan->id.'/'.$detail->qty*$detail->sub_total.'/'.$return->id)}}" type="button" class="btn btn-warning">Continue</a></td>
                          @else
                            <td><a style="width:90px;" href="#" type="button" class="btn btn-default disabled">N/A</a></td>
                          @endif
                        @elseif($detail->type_return == 1 )
                          <?php $credit = App\Models\manage_credit_history::where('salreturndetail_id', $detail->id)->first();?>
                          @if(!$credit)
                            <!-- add  -->
                            <td><a style="width:90px;" href="{{url('sales/salreturn_home/addnext/cdb/'.$detail->id.'/'.$return->id.'/'.$detail->qty*$detail->sub_total)}}" type="button" class="btn btn-warning">Continue</a></td>
                          @else
                            <td><a style="width:90px;" href="#" type="button" class="btn btn-default disabled">N/A</a></td>
                          @endif
                        @else
                          <td><a style="width:90px;" href="#" type="button" class="btn btn-default disabled">N/A</a></td>
                        @endif
                      </tr>
                     @endforeach
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>ID Items</th>
                        <th>Items</th>
                        <th>Quantities</th>
                        <th>Sub Price</th>
                        <th>Sub Total</th>
                        <th>Type</th>
                        <th>Action</th>
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

  <div class="modal fade" id="rejectModalbarang" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#2980b9">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" style="color:white">Item Return</h4>
            </div>
	        <div class="modal-body">
            <form class="form-horizontal" action="{{ url('/sales/salreturn_home/do_addnext') }}" method="post">
              {{-- set token --}}
              {{ csrf_field() }}
              <input type="hidden" id="qtyasli" name="qtyasli">
              <input type="hidden" id="qtysudahreturn" name="qtysudahreturn">
              <input type="hidden" id="stockbarang" name="stockbarang">
              <input type="hidden" id="barangid" name="barangid">
              <input type="hidden" id="returid" name="returid" value="{{$return->id}}">
              <input type="hidden" id="detailpenjualanid" name="detailpenjualanid">
              <div class="modal-body" style="color:black">
                  <div class="row" style="margin:5px;">
                      <table id="table-home" class="table table-bordered table-hover">
                        <thead>
                          <tr>
                            <th>ID Items</th>
                            <th><input type="text" class="form-control name" name="iditemsprimary" id="iditemsprimary" placeholder="New Item" readonly></th>
                          </tr>
                          <tr>
                            <th>Items</th>
                            <th><input type="text" class="form-control name" name="items" id="items" placeholder="New Item" readonly></th>
                          </tr>
                          <tr>
                            <th>Sub Price</th>
                            <th><input type="text" class="form-control name" name="subtotal" id="subtotal" placeholder="New Item" readonly></th>
                          </tr>
                            <th>Quantities</th>
                            <th>
                              <input type="number" required min="0" class="form-control" name="items_qty_view" id="items_qty_view" placeholder="Quantities" value="{{old('items_qty_view')}}" onblur="calculateForm();">
                              @if($errors->has('items_qty_view'))
                                  <p style="font-style: bold; color: red;">{{ $errors->first('items_qty_view') }}</p>
                              @endif
                            </th>
                          </tr>
                          <tr>
                            <th>Grand Total</th>
                            <th><input type="number" min="0" class="form-control" name="items_grand_total_view" id="items_grand_total_view" placeholder="Price Sub" value="{{old('items_sub_total_view')}}" onblur="calculateForm();" readonly></th>
                          </tr>
                          <tr>
                            <th>Type Return</th>
                            <th>
                                <select class="form-control" name="type_return" required  id="type_return">
                                  <option value="">Choose Type Return</option>
                                  <option @if(old('type_return') == '1') selected @endif value="1">Cut the Debt</option>
                                  <option @if(old('type_return') == '2') selected @endif value="2">Change Money</option>
                                  <option @if(old('type_return') == '3') selected @endif value="3">Replace items at the same price</option>
                                  <option @if(old('type_return') == '4') selected @endif value="4">Trade-in Goods</option>
                                </select>
                              @if($errors->has('type_return'))
                                  <p style="font-style: bold; color: red;">{{ $errors->first('type_return') }}</p>
                              @endif</th>
                          </tr>
                        </thead>
                        <tbody>

                        </tbody>
                      </table>
                  </div>
  	          </div>
            <div class="modal-footer">
                <button style="width:90px;" type="submit" onclick="return confirm('Are you sure you want to save this data ?')" class="btn btn-info">Submit</button>
            </div>
            </form>
	        </div>
        </div>
    </div>
  </div>

  <script>

    function barang(id){
      $('#rejectModalbarang').modal();
      $.ajax({
            url: "{{url('sales/salreturn_home/addnext/getbarang')}}" + "/" + id,
            data: {},
            dataType: "json",
            type: "get",
            success:function(data)
            {
              var iditemsprimary = data[0]["iditemsprimary"];
              var items = data[0]["items"];
              var subtotal = data[0]["subtotal"];
              var qtyasli = data[0]["qtyasli"];
              var qtysudahreturn = data[0]["qtysudahreturn"];
              var stockbarang = data[0]["stockbarang"];
              var barangid = data[0]["barangid"];
              var detailpenjualanid = id;

              subtotalrp = subtotal.toFixed(2).replace(/./g, function(c, i, a) {
                return i && c !== "." && ((a.length - i) % 3 === 0) ? ',' + c : c;
              });

                    $('#iditemsprimary').val(iditemsprimary);
                    $('#items').val(items);
                    $('#subtotal').val(subtotal);
                    $('#qtyasli').val(qtyasli);
                    $('#qtysudahreturn').val(qtysudahreturn);
                    $('#stockbarang').val(stockbarang);
                    $('#detailpenjualanid').val(detailpenjualanid);
            }

        });
    }

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/sales/salreturn_home') }}");
      }
    }

    // items select2
    $(".js-penjualan-id").select2({
    ajax: {
      url: "{{ url('/sales/salreturn_home/search_selling') }}",
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

    $(".js-penjualan-id").on("select2:select", function (e) {
      var obj = $(".js-penjualan-id").select2("data")
      $('#buy_id').val(obj[0].id);

      $('#items_id').val(obj[0].items_id);
      $('#items_qty').val(obj[0].qty);
      $('#items_sub_total').val(obj[0].sub_total);

      $('#items_id_view').val(obj[0].items_id);
      $().val(obj[0].qty);
      $('#items_sub_total_view').val(obj[0].sub_total);

      $('#payment_total').val(obj[0].qty * obj[0].sub_total);
      $('#payment_total_view').val(obj[0].qty * obj[0].sub_total);
    });
    // items select2

    $('.for_date').datetimepicker({
      format: 'YYYY/MM/DD'
    });

    var calculateForm = function (){
      var grand = document.getElementById("items_qty_view").value * document.getElementById("subtotal").value;
      document.getElementById("items_grand_total_view").value = Math.round(grand);
    };

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
                  title: 'TUNAS ABADI 8 | List Items in Selling',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | List Items in Selling',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | List Items in Selling',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | List Items in Selling',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed three-column',
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

    // -------------------------------------------------------------------------------

      $('#table-home1 tfoot th').each( function () {
        var title = $(this).text();
        $(this).html( '<input style="width:100%;" type="text" placeholder="Search '+title+'" />' );
      });

      var table = $('#table-home1').DataTable({
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
                  title: 'TUNAS ABADI 8 | List Items in Sales Return',
                  exportOptions: {
                      columns: [ 0, ':visible' ]
                  }
              },
              {
                  extend: 'excelHtml5',
                  pageSize: 'A4',
                  title: 'TUNAS ABADI 8 | List Items in Sales Return',
                  exportOptions: {
                      columns: ':visible'
                  }
              },
              {
                  extend: 'pdfHtml5',
                  pageSize: 'A4',
                  orientation: 'landscape',
                  title: 'TUNAS ABADI 8 | List Items in Sales Return',
                  exportOptions: {
                      columns: ':visible',
                  }
              },
              {
                extend: 'print',
                pageSize: 'A4',
                title: 'TUNAS ABADI 8 | List Items in Sales Return',
                exportOptions: {
                    columns: ':visible'
                }
              },
              {
                extend: 'colvis',
                collectionLayout: 'fixed three-column',
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
