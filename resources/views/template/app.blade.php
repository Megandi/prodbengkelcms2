<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | Backsite</title>

    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">


    {{-- CSS and Plugins --}}

    {{-- datetimepicker --}}
    <link rel="stylesheet" href="{{ url('adminlte/extension/bootstrap-datetimepicker.min.css') }}">

    <!-- Bootstrap 3.3.6 -->
    <link rel="stylesheet" href="{{ url('adminlte/bootstrap/css/bootstrap.min.css') }}">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/font-awesome/css/font-awesome.min.css') }}">
    <!-- Ionicons -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/ionicons/css/ionicons.min.css') }}">
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
    <!-- Theme style -->
    <link rel="stylesheet" href="{{ url('adminlte/dist/css/AdminLTE.min.css') }}">
    <!-- Select2 style -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/select2/select2.min.css') }}">
    <!-- iCheck -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/iCheck/square/blue.css') }}">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="{{ url('adminlte/dist/css/skins/_all-skins.min.css') }}">

    <!-- DataTables -->
    <link rel="stylesheet" href="{{ url('adminlte/plugins/datatables/dataTables.bootstrap.css') }}">
    <link rel="stylesheet" href="{{ url('adminlte/extension/buttons.dataTables.min.css') }}">

    {{-- javascript and jquery --}}

    <!-- jQuery 2.2.3 -->
    <script src="{{ url('adminlte/plugins/jQuery/jquery-2.2.3.min.js') }}"></script>    
    {{-- select2 --}}
    <script src="{{ url('adminlte/plugins/select2/select2.min.js') }}"></script>

    {{-- datetimepicker --}}
    <script src="{{ url('adminlte/extension/moment.js') }}"></script>
    <script src="{{ url('adminlte/extension/transition.js') }}"></script>
    <script src="{{ url('adminlte/extension/collapse.js') }}"></script>
    <script src="{{ url('adminlte/extension/bootstrap-datetimepicker.js') }}"></script>

</head>

    <body class="hold-transition skin-blue sidebar-mini">
        @yield('content')
    </body>

    <!-- Bootstrap 3.3.6 -->
    <script src="{{ url('adminlte/bootstrap/js/bootstrap.min.js') }}"></script>
    <!-- iCheck -->
    <script src="{{ url('adminlte/plugins/iCheck/icheck.min.js') }}"></script>
    <!-- SlimScroll -->
    <script src="{{ url('adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
    <!-- FastClick -->
    <script src="{{ url('adminlte/plugins/fastclick/fastclick.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ url('adminlte/dist/js/app.min.js') }}"></script>

    <script src="{{ url('adminlte/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ url('adminlte/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>

    <script src="{{ url('adminlte/extension/pdfmake.min.js') }}"></script>
    <script src="{{ url('adminlte/extension/vfs_fonts.js') }}"></script>
    <script src="{{ url('adminlte/extension/buttons.print.min.js') }}"></script>

    <script src="{{ url('adminlte/extension/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('adminlte/extension/buttons.flash.min.js') }}"></script>
    <script src="{{ url('adminlte/extension/jszip.min.js') }}"></script>
    <script src="{{ url('adminlte/extension/buttons.html5.min.js') }}"></script>
    <script src="{{ url('//cdn.datatables.net/buttons/1.3.1/js/buttons.colVis.min.js') }}"></script>

    <script>

        $("input[type=number]").keyup(function(e) {
            var value = $(this).val();
            var regex_cell = new RegExp('[^0-9]','g');
            var new_value;
            if (!isNumeric(value, regex_cell))
            {
                new_value = value.replace(regex_cell, '');
                // alert(new_value);
                $(this).val(new_value);
            }

            if (new_value<0)
            {
                new_value = new_value*-1;
                // alert(new_value);
                $(this).val(new_value);
            }

            if (e.which !== 0) {
                if(e.which==188){
                    $(this).val("");
                }
            }

            function isNumeric(elem, regex_cell) {
                if(elem.match(regex_cell)){
                    return true;
                }else{
                    return false;
                }
            }
        });

        $("input[type=text]").keypress(function(event){
        var inputValue = event.which;
        // console.log(event.which);
        // allow letters and whitespaces only.
        if(!(inputValue >= 48 && inputValue <= 57||inputValue >= 65 && inputValue <= 122 || inputValue == 45) && (inputValue != 32 && inputValue != 0) || (inputValue == 96 || inputValue == 95 || inputValue == 94 || inputValue == 93 || inputValue == 92 || inputValue == 91)) { 
                event.preventDefault(); 
            }
        });

        $("input[type=email]").keypress(function(event){
        var inputValue = event.which;
        // console.log(event.which);
        // allow letters and whitespaces only.
        if(!(inputValue >= 48 && inputValue <= 57 || inputValue >= 64 && inputValue <= 122 || inputValue == 95 || inputValue == 45 || inputValue == 46) && (inputValue != 32 && inputValue != 0) || (inputValue == 96 || inputValue == 94 || inputValue == 93 || inputValue == 92 || inputValue == 91)) { 
                event.preventDefault(); 
            }
        });

        $("input[type=password]").keypress(function(event){
        var inputValue = event.which;
        // console.log(event.which);
        // allow letters and whitespaces only.
        if(!(inputValue >= 48 && inputValue <= 57||inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0) || (inputValue == 96 || inputValue == 95 || inputValue == 94 || inputValue == 93 || inputValue == 92 || inputValue == 91)) { 
                event.preventDefault(); 
            }
        });

        $(".area").keypress(function(event){
        var inputValue = event.which;
        // console.log(event.which);
        // allow letters and whitespaces only.
        if(!(inputValue >= 48 && inputValue <= 57||inputValue >= 65 && inputValue <= 122 || inputValue == 45 || inputValue == 46) && (inputValue != 32 && inputValue != 0) || (inputValue == 96 || inputValue == 95 || inputValue == 94 || inputValue == 93 || inputValue == 92 || inputValue == 91)) { 
                event.preventDefault(); 
            }
        });

        $(".name").keypress(function(event){
        var inputValue = event.which;
        // console.log(event.which);
        // allow letters and whitespaces only.
        if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0) || (inputValue == 96 || inputValue == 95 || inputValue == 94 || inputValue == 93 || inputValue == 92 || inputValue == 91)) { 
                event.preventDefault(); 
            }
        });
    </script>

</html>
