@extends('ajax.b4')
@section('content')
<div class="container">
    <input type="text" name="search" id="search" class="form-control" placeholder="Search Users" />
</div>
<div class="table-responsive">
    <h3 align="center"> Total Data : <span id="total_records"></span></h3>
</div>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>User</th>
        </tr>
    </thead>
    <tbody>

    </tbody>
</table>

@endsection