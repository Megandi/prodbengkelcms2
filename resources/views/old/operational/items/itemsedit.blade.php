@extends('template.app')

{{-- set title --}}
@section('title', 'Items Edit')

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
          Items
          <small>Edit</small>
        </h1>
        <ol class="breadcrumb">
          <li>Dashboard</li>
          <li>Items Home</li>
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

                <form class="form-horizontal" action="{{ url('/operational/items_home/do_edit/'.$ms_barang->id) }}" method="post">
                  <div class="box-body">

                    {{-- set token --}}
                    {{ csrf_field() }}

                    <div class="form-group">
                      <label for="input_items_id" class="col-sm-2 control-label">Items ID <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="items_id" id="items_id" placeholder="Items ID" value="{{$ms_barang->items_id}}" readonly>
                      @if($errors->has('items_id'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_id') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_items_name" class="col-sm-2 control-label">Name <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control name" name="items_name" id="items_name" placeholder="Name" value="{{$ms_barang->nama}}">
                      @if($errors->has('items_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('items_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_category_name" class="col-sm-2 control-label">Category <span style="color:red;">*</span></label>
                      <div class="col-sm-10">
                        <select class="form-control" name="category_name" id="category_name">
                        <option value="">Choose Category</option>
                          @foreach($ms_kategori_barang as $item)
                            @if( $ms_barang->kategori == $item->id)
                              <option selected value="{{$ms_barang->kategori}}">{{ $ms_barang->category_name }}</option>
                            @else
                              <option value="{{$item->id}}">{{$item->name}}</option>
                            @endif
                          @endforeach
                        </select>
                      @if($errors->has('category_name'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('category_name') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_spec" class="col-sm-2 control-label">Spesification <span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <textarea style="resize: none;" class="form-control" rows="3" name="spec" id="spec" placeholder="Spesification">{{$ms_barang->spesifikasi}}</textarea>
                      @if($errors->has('spec'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('spec') }}</p>
                      @endif
                      </div>
                    </div>

                    <div class="form-group">
                      <label for="input_price_sell" class="col-sm-2 control-label">Sub Price Selling<span style="color:red;"></span></label>
                      <div class="col-sm-10">
                        <input type="number" min="0" class="form-control" name="price_sell" id="price_sell" placeholder="Sub Price Selling" value="{{$ms_barang->harga_jual}}">
                      @if($errors->has('price_sell'))
                          <p style="font-style: bold; color: red;">{{ $errors->first('price_sell') }}</p>
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
            </div>
        </div>
      </div>
    </section>
        <!-- /.content -->

  </div>

  <script>

    function close_window() {
      if (confirm("Are you sure you want to close this page? Any changes you made will not be saved.")) {
        window.location.replace("{{ url('/operational/items_home') }}");
      }
    }

  </script>
</body>
@endsection