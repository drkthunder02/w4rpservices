@extends('layouts.user.dashb4');
@section('content')
<br>
@foreach($bodies as $body)
{{ $body }}<br>
@endforeach
@endsection