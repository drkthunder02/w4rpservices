@extends('layouts.b4')
@section('content')
<div class="container">
    <h1>Work in Progress aka NOTHING WORKS!</h1><br>
    <h2>Fleets</h2>
    <?php var_dump($data); ?>
    @for($i = 0; $i < count($data['fc']); $i++)
        <a href="{{ route('addpilot', $data[1][$i], [Auth::user()->character_id]) }}">Join {{ $data[2][$i] }}</a>
    @endfor
</div>
@endsection