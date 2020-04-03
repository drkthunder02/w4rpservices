@extends('layouts.admin.b4')
@section('content')
<!-- Under Review Section Guage Chart -->
<div class="container">
    <div id="under-review-div"></div>
    {!! $lava->render('GaugeChart', 'SRP', 'under-review-div') !!}
</div>
@endsection