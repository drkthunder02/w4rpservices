<!DOCTYPE html>
<html lang="en">
@include('layouts.admin.dashboard.head')
<body class="hold-transition sidebar-mini">
<div class="wrapper">
@include('layouts.admin.dashboard.navbar')
@include('layouts.admin.dashboard.main')
@include('layouts.admin.dashboard.content')
@include('layouts.admin.dashboard.control')
@include('layouts.admin.dashboard.footer')
</div>
<!-- ./wrapper -->
@include('layouts.admin.dashboard.scripts')
</body>
</html>
