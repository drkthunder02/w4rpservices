@include('jumpbridges.corp.ajaxb4')
@section('content')

<div class="container">
    <div class="card">
        <div class="card-header">
            <h2>Overall Jump Bridge Usage for Warped Intentions</h2>
        </div>
        <div class="card-body">
            {!! Form::open(['action' => 'getMessage()']) !!}
                <div class="form-group col-md-6">
                    {{ Form::label('days', 'Time in Days') }}
                    {{ Form::selct('days', [
                        '30' => '30',
                        '60' => '60',
                        '90' => '90',
                        ], '30') }}
                </div>
            {{ Form::submit('Submit', ['class' => 'btn btn-primary']) }}
            {!! Form::close() !!}
        </div>
    </div>
</div>

@endsection