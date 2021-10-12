@extends('web.layout')

@section('content')
<div class="d-flex flex-row">
    <div class="card m-5 w-100">
        <div class="card-body w-100">
            <h3 class="card-title">{{ $template }}</h3>            
        </div>
        <button class="btn btn-primary" onclick='window.location.reload();'>Neue Nachricht generieren!</button>
    </div>
</div>
@stop