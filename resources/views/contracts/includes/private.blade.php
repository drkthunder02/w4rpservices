<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-sm" align="left">
                            {{ $contract['title'] }}
                        </div>
                        <div class="col-sm" align="center">
                            Type: Private
                        </div>
                        <div class="col-sm" align="right">
                            <a href="/contracts/display/newbid/{{ $contract['contract_id'] }}" class="btn btn-primary" role="button">Bid on Contract</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="container">
                        End Date: {{ $contract['end_date'] }}
                    </div>
                    <span class="border-dark">
                        <div class="container">
                            {{ $contract['body'] }}
                        </div>
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
<br>