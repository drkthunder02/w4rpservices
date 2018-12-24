<script>
    function getMessage(days){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $.ajax({
            type:'POST',
            url:'/jumpbridges/getoverall',
            data: {
                days: time
            },
            success:function(response){
                $("#overall").html(response);
                console.log('Ajax Call Successful');
            },
            fail:function(response) {
                alert('Unable to fulfill request.');
            }
        });
    }
</script>