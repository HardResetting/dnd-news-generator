@extends('web.layout')

@section('content')

<script>
    function addVar(event) {
        document.getElementById("value").value += '[@var_X(Min,Max)]';
    }

    function addType(event) {
        var e = document.getElementById("type_name");
        var type_name = e.value;
        document.getElementById("value").value += '[' + type_name + ']';
    }

    function addType_mult(event) {
        var e = document.getElementById("type_name");
        var type_name = e.value;
        document.getElementById("value").value += '[@var_X,' + type_name + ']';
    }

    function addChoice(event) {
        document.getElementById("value").value += '[?@var_X,\'Wahl1\',\'Wahl2\']';
    }
</script>

<div class="card m-5">
    <div class="card-body">
        <h3 class="card-title">Template bearbeiten</h3>

        <div class="btn-group p-4 gap-1" role="group">
            <button onclick="addVar(event)" class="btn btn-secondary">Variable einfügen</button>
            <button onclick="addChoice(event)" class="btn btn-secondary">Wahl einfügen</button>
            <div class="btn-group" role="group">
                <select class="dropdown-toggle" id="type_name" name="type_name">
                    @foreach($types as $key => $type)
                    <option value="{{$type->name}}">{{$type->name}}</option>
                    @endforeach
                </select>
            </div>
            <button onclick="addType(event)" class="btn btn-secondary">Typ-Namen einfügen</button>
            <button onclick="addType_mult(event)" class="btn btn-secondary">Anzahl Typ einfügen</button>
        </div>

        <div class="p-4 gap-2 w-100">
            <form id="editForm" data-ajax-url="{{ route('api.templates.update', $template->id) }}" data-ajax-form="#editForm" data-ajax-method="POST" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $template->id }}">
                <div class="form-group mb-3">
                    <label for="value">Template</label>
                    <textarea class="form-control" name="value">{{ $template->value }}</textarea>
                </div>

            </form>

            <div class="d-flex justify-content-end">
                <button id="save" type="button" class="btn btn-primary mx-2" data-url="{{ route('templates.index') }}" data-ajax-method="POST">Save</button>
                <button id="saveAndGenerate" type="button" class="btn btn-primary mx-2" data-url="{{ route('templates.generate', $template->id ) }}">Save and generate</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.DNG.Template = window.DNG.Template || {};
    window.DNG.Template.Edit = window.DNG.Template.Edit || {};

    window.DNG.Template.Edit.Save = function(method) {
        var el = document.getElementById("editForm")
        return window.DNG.Generic.ElementAjaxCaller(el, null, null, {
            onSuccess: method
        });
    }

    document.getElementById("save").addEventListener("click", function() {
        var el = this;
        window.DNG.Template.Edit.Save(function() {
            window.location.href = el.dataset.url;
        })
    });

    document.getElementById("saveAndGenerate").addEventListener("click", function() {
        var el = this;
        window.DNG.Template.Edit.Save(function() {
            window.location.href = el.dataset.url;
        })
    });
</script>
@stop