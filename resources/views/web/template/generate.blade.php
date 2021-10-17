@extends('web.layout')

@section('content')
<div class="d-flex flex-column mx-5 mt-5">
    <div class="card w-100">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h3 class="m-0">Template</h3>
                <a class="btn btn-primary" href="{{ route('templates.edit', $templateId) }}">
                    <i class="fas fa-edit"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            <span class="card-title">{{ $templateString }}</span>
        </div>
    </div>
    <a id="NewTemplate" class="btn btn-primary mt-1" href="{{ route('templates.generateRandom') }}">
        Get new Template
    </a>
</div>

<div class="d-flex flex-column w-100 text-center mt-4">
    <i class="fas fa-angle-double-down fa-2x"></i>
</div>

<div class="d-flex flex-column mx-5 mt-4">
    <div class="card w-100 mt-3">
        <div class="card-header">
            <h3 class="m-0">Result</h3>
        </div>
        <div class="card-body">
            <span id="Result" data-ajax-url="{{ route('api.templates.compile', $templateId) }}" data-ajax-update="#Result" class="card-title">
                {{ $result }}
            </span>
        </div>
    </div>
    <button id="Recompile" class="btn btn-secondary mt-1">
        Redo same Template
    </button>
</div>

<script>
    window.DNG.Templates = window.DNG.Templates || {};

    window.DNG.Templates.Recompile = function() {
        element = document.getElementById("Result")
        window.DNG.Generic.ElementAjaxCaller(element);
    }

    document.getElementById("Recompile").addEventListener("click", function() {
        window.DNG.Templates.Recompile();
    })
</script>
@stop