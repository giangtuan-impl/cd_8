<div class="panel panel-default">
    @if (session('success'))
        <div class="alert alert-success flash-alert" role="alert" style="text-align: center;">
            {{ session('success') }}
        </div>
    @endif
    @if (session('warning'))
        <div class="alert alert-warning flash-alert" role="alert" style="text-align: center;">
            {{ session('warning') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger flash-alert" role="alert" style="text-align: center;">
            {{ session('error') }}
        </div>
    @endif
</div>
