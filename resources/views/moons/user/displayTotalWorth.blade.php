@extends('layouts.b4')
@section('content')
<div class="container">
    <h2>Total Worth of the Moon / Month</h2>
    <div class="jumbotron">
        Total Moon Goo + Ore Worth: <?php echo $totalWorth; ?> ISK<br>
        Total Moon Goo Worth: <?php echo $totalGoo; ?> ISK<br>
    </div>
</div>
@endsection
