@extends('layouts.user.dashb4');
@section('content')
<br>
@foreach($bodies as $body)
{{ var_dump($body) }}<br>
@endforeach
@endsection