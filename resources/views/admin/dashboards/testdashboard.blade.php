@extends('layouts.admin.b4')
@section('content')
<div class="container">
<h2>Admin Dashboard Test</h2><br>
<div class="card">
    <div class="card-header">
        <h2>user Information</h2>
    </div>
    <div class="card-body">
        Form<br>
        <table class="table table-striped table-bordered">
            <thead>
                <th>Name</th>
                <th>Role</th>
                <th>Permissions</th>
                <th>Action</th>
            </thead>
            <tbody>
                @for ($i = 0; $i < 50; $i++)
                <tr>
                    <td>Name</td>
                    <td>Role</td>
                    <td>Permissions</td>
                    <td>Form</td>
                </tr> 
                @endfor
            </tbody>
        </table>
    </div>
</div>
@endsection