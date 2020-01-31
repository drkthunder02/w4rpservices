@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Welcome to the W4RP Services Page</div>
                <div class="card-body">
                    <div class="container" align="center">
                        <img src={{asset('/img/eve-sso-login-white-large.png')}}
                        <a href="/login">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection