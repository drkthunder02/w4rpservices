<div class="container-fluid">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="/dashboard">W4RP</a>
        <ul class="navbar-nav mr-auto">
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Moons</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    @if(auth()->user()->hasRole('Guest'))
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    @endif
                    @if(auth()->user()->hasRole('User'))
                    <a class="dropdown-item" href="/moons/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/worth">Moon Worth</a>
                    @endif
                    @if(auth()->user()->hasRole('Admin'))
                    <a class="dropdown-item" href="/moons/admin/display">Display Moons</a>
                    <a class="dropdown-item" href="/moons/display/worth">Moon Worth</a>
                    <a class="dropdown-item" href="/moons/admin/addmoon">Add Moon</a>
                    <a class="dropdown-item" href="/moons/admin/updatemoon">Update Moon</a>
                    <a class="dropdown-item" href="/moons/admin/journal">Journal</a>
                    @endif
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Wiki</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="https://www.w4rp.space">Wiki</a>
                    <a class="dropdown-item" href="/wiki/register">Registration</a>
                    <a class="dropdown-item" href="/wiki/changepassword">Change Password</a>
                </div>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Statistics</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/jumpbridges/overall">Jump Bridge Usage</a>
                </div>
            </li>
            @if(auth()->user()->hasPermission('logistics.minion') && 0)
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Logistics</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/logistics/contracts/available">Available Contracts</a>
                    <a class="dropdown-item" href="/logistics/contracts/completed">Completed Contracts</a>
                    <a class="dropdown-item" href="/logistics/insurance/request">Insurance Request</a>
                </div>
            </li>
            @endif
            @if(auth()->user()->hasPermission('structure.operator'))
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdoownMenuLink" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Structures</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropDownMenuLink">
                    <a class="dropdown-item" href="/structures/taxes/display">Current Taxes</a>
                    <a class="dropdown-item" href="/structures/register">Register Structure</a>
                    @if(auth()->user()->hasRole('Admin'))
                    <a class="dropdown-item" href="/structures/admin/taxes/display">Corp Taxes</a>
                    <a class="dropdown-item" href="/structures/admin/taxes/industry">Industry Taxes</a>
                    <a class="dropdown-item" href="/structures/admin/taxes/reprocessing">Reprocessing Taxes</a>
                    <a class="dropdown-item" href="/finances/admin">Finances</a>
                    @endif
                </div>
            </li>
            @endif
        </ul>
        <ul class="navbar-nav m1-auto">
            <li class="nav-item">
                <a class="nav-link" href="/scopes/select">Add Esi Scopes</a>
            </li>
            @if(auth()->user()->hasRole('Admin'))
            <li class="nav-item">
                <a class="nav-link" href="/admin/dashboard">Admin</a>
            </li>
            @endif
            <li class="nav-item">
                <a class="nav-link" href="/logout">Logout</a>
            </li>
        </ul>
    </nav>
</div>