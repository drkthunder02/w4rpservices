@extends('layouts.admin.b4')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h2>Create New Contract</h2>
    </div>
</div>
<br>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    New Contracts
                </div>
                <div class="card-body">
                    {!! Form::open(['action' => 'Contracts\ContractAdminController@storeNewContract', 'method' => 'POST']) !!}
                        <div class="form-group">
                            {{ Form::label('name', 'Contract Name') }}
                            {{ Form::text('name', '', ['class' => 'form-control', 'placeholder' => 'Some Name']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('body', 'Description') }}
                            {{ Form::textarea('body', '', ['class' => 'form-control']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('date', 'End Date') }}
                            {{ Form::date('date', \Carbon\Carbon::now()->addWeek(), ['class' => 'form-control', 'placeholder' => '4/24/2019']) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('type', 'Public Contract') }}
                            {{ Form::radio('type', 'Public', true) }}
                        </div>
                        <div class="form-group">
                            {{ Form::label('type', 'Private Contract') }}
                            {{ Form::radio('type', 'Private', false) }}
                        </div>
                    {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection