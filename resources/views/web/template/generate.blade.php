@extends('web.layout')

@section('content')
<div class="d-flex flex-column mx-5 mt-5">
    <div class="card w-100">
        <div class="card-header">
            Template
        </div>
        <div class="card-body">
            <h5 class="card-title">{{ $templateString }}</h5>
            <!-- ignore VS-Code Error -->
        </div>
    </div>
    <button id="NewTemplate" class="btn btn-primary mt-1">
        Get new Template
    </button>
</div>

<div class="d-flex flex-column w-100 text-center mt-4">
    <i class="fas fa-angle-double-down fa-2x"></i>
</div>

<div class="d-flex flex-column mx-5 mt-4">
    <div class="card w-100 mt-3">
        <div class="card-header">
            Result
        </div>
        <div class="card-body">
            <h5 id="Result" data-ajax-url="{{ route('api.templates.generate', $templateId) }}" data-ajax-update="#Result" class="card-title">
                {{ $result }}
            </h5>
        </div>
    </div>
    <button id="Recompile" class="btn btn-secondary mt-1">
        Recompile same Template
    </button>
</div>

<script>
    window.DNG.Templates = window.DNG.Templates || {};

    window.DNG.Templates.Recompile = function() {
        element = document.getElementById("Result")
        window.DNG.Generic.ElementAjaxCaller(element);
    }

    document.getElementById("NewTemplate").addEventListener("click", function() {
        window.location.reload();
    })
    document.getElementById("Recompile").addEventListener("click", function() {
        window.DNG.Templates.Recompile();
    })
</script>
@stop