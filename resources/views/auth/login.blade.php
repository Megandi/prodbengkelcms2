@extends('template.app')

@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{url('/')}}"><b>MG</b> System</a>
        </div>
            <!-- /.login-logo -->
        <div style="background: #f2f3f4;" class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>

            <form role="form" method="POST" action="{{ route('login') }}">
            {{ csrf_field() }}

                <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }} has-feedback">
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email" pattern="[a-z0-9._+-]+@[a-z0-9.-]+\.[a-z]{2,3}$" required autofocus>
                    @if ($errors->has('email'))
                        <span class="help-block">
                            <strong>{{ $errors->first('email') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }} has-feedback">
                    <input id="password" type="password" class="form-control" name="password" placeholder="Password" pattern="[A-Za-z0-9]+" required>
                    @if ($errors->has('password'))
                        <span class="help-block">
                            <strong>{{ $errors->first('password') }}</strong>
                        </span>
                    @endif
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="pull-right col-xs-4">
                      <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                    </div><!-- /.col -->
                </div>
            </form>
        </div>
    </div>

@endsection