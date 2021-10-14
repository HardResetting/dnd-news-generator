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
                    <th scope="col">Wert</th>
                    <th scope="col">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($templates as $key => $templates)
                <tr>
                    <td>{{$templates->id}}</th>
                    <td>{{$templates->value}}</th>
                    <td class="btn-group w-100 text-right">
                        <form action="{{ route('templates.edit', $templates->id ) }}" method="POST">
                            @method('GET')
                            @csrf
                            <button class="btn btn-primary">Bearbeiten</button>
                        </form>
                        <form action="{{ route('templates.destroy', $templates->id ) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button onclick="return confirm('Template mit der ID \'{{ $templates->id }}\' wirklich löschen?')" class="btn btn-danger">Löschen</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop