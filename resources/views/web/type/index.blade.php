@extends('web.layout')

@section('content')
<div class="d-flex flex-row">
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
                    <th scope="col">Name</th>
                    <th scope="col">Aktionen</th>
                </tr>
            </thead>
            <tbody>
                @foreach($types as $key => $types)
                <tr>
                    <td>{{$types->id}}</th>
                    <td>{{$types->name}}</th>
                    <td class="btn-group w-100 text-right">
                        <form action="{{ route('types.edit', $types->id ) }}" method="POST">
                            @method('GET')
                            @csrf
                            <button class="btn btn-primary">Bearbeiten</button>
                        </form>
                        <form action="{{ route('types.destroy', $types->id ) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            <button onclick="return confirm('\'{{ $types->name }}\' wirklich löschen?')" class="btn btn-danger">Löschen</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop