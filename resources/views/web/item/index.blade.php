@extends('web.layout')

@section('content')
<div class="d-flex flex-row">

    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Item hinzufügen</h3>
            <div class="d-flex p-4 gap-2">
                <form action="{{ route('items.store') }}" method="post">
                    @method('POST')
                    @csrf
                    <div class="form-group">
                        <label for="singular">Singular</label>
                        <input class="form-control" id="singular" name="singular"></input>
                    </div>
                    <div class="form-group">
                        <label for="plural">Plural</label>
                        <input class="form-control" id="plural" name="plural"></input>
                    </div>
                    <div class="form-group">
                        <label for="type_id">Typ</label>
                        <select class="form-control" id="type_id" name="type_id">
                            @foreach($types as $key => $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Typ hinzufügen</h3>
            <div class="d-flex p-4">
                <form action="{{ route('types.store') }}" method="post">
                    @method('POST')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name"></input>
                    </div>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
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
                    <th scope="col">Singular</th>
                    <th scope="col">Plural</th>
                    <th scope="col">Typ</th>
                    <th scope="col">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $key => $items)
                <tr>
                    <td>{{$items->id}}</th>
                    <td>{{$items->singular}}</th>
                    <td>{{$items->plural}}</th>
                    <td>{{$items->type_id}}</th>
                    <td class="btn-group w-100 text-right">
                        <form action="{{ route('items.edit', $items->id ) }}" method="POST">
                            @method('GET')
                            @csrf
                            <button class="btn btn-primary">Bearbeiten</button>
                        </form>
                        <form action="{{ route('items.destroy', $items->id ) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button onclick="return confirm('\'{{ $items->singular }}\' wirklich löschen?')" class="btn btn-danger">Löschen</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop