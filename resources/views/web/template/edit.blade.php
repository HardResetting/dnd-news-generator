@extends('web.layout')

@section('content')
<style>
    .cm-template-number {
        color: chartreuse;
    }
    .cm-template-string {
        color: #a11;
    }
    .cm-template-variable {
        color: #219;
    }
    .cm-template-constant {
        color: #219;
    }
    .cm-template-table {
        color: #708;
    }
    .cm-template-brackets {
        color: #98673F;
    }
    .cm-template-method {
        color: #07ab77;
    }
    .cm-template-error {
        color: red;
    }

</style>
<div class="card m-5">
    <div class="card-body">
        <h3 class="card-title">Template bearbeiten</h3>

        <div class="btn-group p-4 gap-1" role="group">
            <button class="btn btn-secondary">Variable einfügen</button>
            <button class="btn btn-secondary">Wahl einfügen</button>
            <div class="btn-group" role="group">
                <select class="dropdown-toggle" id="type_name" name="type_name">
                    @foreach($types as $key => $type)
                    <option value="{{$type->name}}">{{$type->name}}</option>
                    @endforeach
                </select>
            </div>
            <button class="btn btn-secondary">Typ-Namen einfügen</button>
            <button class="btn btn-secondary">Anzahl Typ einfügen</button>
        </div>

        <div class="p-4 gap-2 w-100">
            <form id="editForm" data-ajax-url="{{ route('api.templates.update', $template->id) }}" data-ajax-form="#editForm" data-ajax-method="POST" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $template->id }}">
                <input type="hidden" id="formValue" name="value" value="{{ $template->value }}">

                <div id="textareaContainer" class="form-group mb-3"></div>

            </form>

            <div class="d-flex justify-content-end">
                <button id="save" type="button" class="btn btn-primary mx-2" data-url="{{ route('templates.index') }}">Save</button>
                <button id="saveAndGenerate" type="button" class="btn btn-primary mx-2" data-url="{{ route('templates.generate', $template->id ) }}">Save and generate</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.DNG.Template = window.DNG.Template || {};
    window.DNG.Template.Edit = window.DNG.Template.Edit || {};

    window.DNG.Template.Edit.CreateCodeMirrorForm = (value) => {
        window.DNG.Template.Edit.CodeMirrorForm = CodeMirror(document.getElementById("textareaContainer"), {
            value: value,
            mode: "templateEngine",
            inputStyle: "textarea",
            lineWrapping: true,
            autoCloseBrackets: true,
            matchBrackets: true
        });
    }

    window.DNG.Template.Edit.Save = function(method) {
        var formValue = document.getElementById("formValue");
        formValue.value = window.DNG.Template.Edit.CodeMirrorForm.getValue();

        var el = document.getElementById("editForm");

        return window.DNG.Generic.ElementAjaxCaller(el);
    }

    document.getElementById("save").addEventListener("click", function() {
        var el = this;
        window.DNG.Template.Edit.Save().then(function() {
            window.location.href = el.dataset.url;
        })
    });

    document.getElementById("saveAndGenerate").addEventListener("click", function() {
        var el = this;
        window.DNG.Template.Edit.Save().then(function() {
            window.location.href = el.dataset.url;
        })
    });

    window.DNG.Template.Edit.CreateCodeMirrorForm("{!! $template->value !!}");
</script>
@stop