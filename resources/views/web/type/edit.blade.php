@extends('web.layout')

@section('content')
<div class="d-flex flex-row">
    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Typ ändern</h3>
            <div class="d-flex p-4 gap-2">
                <form action="{{ route('types.update', $type->id) }}" method="post">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name" value="{{ $type->name }}"></input>
                    </div>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    </div>
</div>
@stop