@extends('web.layout')

@section('content')
<div class="d-flex flex-column m-5">
    <div class="card w-100">
        <div class="card-header">
            Template
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $templateString }}</h5>
            <!-- ignore VS-Code Error -->
        </div>
    </div>

    <div class="d-flex flex-column w-100 m-3 text-center">
        <i class="fas fa-angle-double-down fa-2x"></i>
    </div>

    <div class="card w-100">
        <div class="card-header">
            Result
        </div>
        <div class="card-body">
            <h5 id="result" class="card-title">{{ $result }}</h5>
        </div>
    </div>

    <button id="Recompile" class="btn btn-primary mt-3" data-ajax-url="{{ route('api.templates.generate', $templateId) }}" data-ajax-update="#result">
        Recompile same Template
    </button>
</div>

<script>
    document.getElementById("Recompile").addEventListener("click", function(){
        window.DNG.Generic.ElementAjaxCaller(this);
    })
</script>
@stop