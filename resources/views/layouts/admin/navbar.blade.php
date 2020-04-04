<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspop="true" aria-expanded="false">General Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/admin/dashboard/users">Users</a>
                    <a class="dropdown-item" href="/admin/dashboard/taxes">Taxes</a>
                    <a class="dropdown-item" href="/admin/dashboard/logins">Allowed Logins</a>
                    <a class="dropdown-item" href="/admin/dashboard/wiki">Wiki</a>
                    <a class="dropdown-item" href="/admin/dashboard/journal">Wallet Journal</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspop="true" aria-expanded="false">Flex Structures</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/flex/display">Display</a>
                    <a class="dropdown-item" href="/flex/display/add">Add</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('moon.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moon Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/moons/admin/display/rentals">Display Moons</a>
                    <a class="dropdown-item" href="/moons/admin/updatemoon">Update Moon</a>
                    <a class="dropdown-item" href="/moons/admin/display/request">Moon Request</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('srp.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">SRP Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/srp/admin/display">SRP Admin Dashboard</a>
                    <a class="dropdown-item" href="/srp/admin/statistics">SRP Statistics</a>
                    <a class="dropdown-item" href="/srp/admin/costcodes/display">SRP Admin Cost Codes</a>
                    <a class="dropdown-item" href="/srp/admin/display/history">SRP History</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('contract.admin'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropDownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Contract Admin</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/contracts/admin/display">Admin Dashboard</a>
                    <a class="dropdown-item" href="/contracts/admin/new">New Contract</a>
                </div>
            </li>
            @endif
        </ul>
        <ul class="navbar-nav m1-auto">
            <li class="nav-item">
                <a class="nav-link" href="/scopes/select">Add Esi Scopes</a>
            </li>
            @if(auth()->user()->hasRole('Admin') || auth()->user()->hasPermission('contract.admin') || auth()->user()->hasPermission('moon.admin') || auth()->user()->hasPermission('srp.admin'))
            <li class="nav-item">
                <a class="nav-link" href="/dashboard">Dashboard</a>
            </li>
            @endif
        </ul>
    </nav>
</div>
<br>