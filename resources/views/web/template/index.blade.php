@extends('web.layout')

@push('head')
<script src="{{ asset('js/toolbar.js')}}"></script>
@endpush

@section('content')

<div class="card m-5">
    <div class="card-body">
        <h3 class="card-title">Template hinzufügen</h3>

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
            <form action="{{ route('templates.store') }}" method="post">
                @method('POST')
                @csrf
                <div class="form-group">
                    <label for="value">Template</label>
                    <textarea class="form-control" id="value" name="value"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
</div>

<div class="card m-5">
    <div class="card-body">
        <h3 class="card-title">{{ $title ?? 'DND News Generator' }}</h3>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Value</th>
                    <th scope="col">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $template)
                <tr>
                    <td>{{$template->id}}</th>
                    <td>{{$template->value}}</th>
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-primary me-2 generate" href="{{ route('templates.generate', $template->id ) }}">Generate</a>
                            <a class="btn btn-primary me-2 edit" href="{{ route('templates.edit', $template->id ) }}">Edit</a>
                            <a class="btn btn-primary me-2 delete" href="{{ route('templates.destroy', $template->id ) }}" onclick='return window.confirm("Delete Template \"{{ $template->value }}\" ?")' data-ajax-replace="#">Delete</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop