<!DOCTYPE html>
<html lang="en">
@include('layouts.user.dashboard.head')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
@include('layouts.user.dashboard.navbar')
@include('layouts.user.dashboard.main')
@include('layouts.user.dashboard.content')
@include('layouts.user.dashboard.control')
@include('layouts.user.dashboard.footer')
</div>
<!-- ./wrapper -->
@include('layouts.user.dashboard.scripts')
</body>
</html>
