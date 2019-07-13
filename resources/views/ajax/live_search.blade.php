@extends('layouts.b4')
@section('content')
<div class="container">
    <input type="text" name="search" id="search" class="form-control" placeholder="search Users" />
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

<script>
    $(document).ready(function() {
        function fetch_user_data(query = '') {
            $.ajax({
                url:"{{ route('live_search.action') }}",
                method:'GET',
                data:{query:query},
                dataType:'json',
                success:function(data) {
                    $('tbody').html(data.table_data);
                    $('#total_records').text(data.total_data);
                }
            });
        }

        fetch_user_data();
    })
</script>

@endsection