@extends('layouts.user.dashb4')
@section('content')
<br>
<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Financial Outlook for the Alliance</h2>
        </div>
        <div class="card-body">
            <div id="finances-div"></div>
            {!! $lava->render('ComboChart', 'Finances', 'finances-div') !!}
        </div>
    </div>
</div>
@endsection