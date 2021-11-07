@extends('web.layout')

@section('content')
<div class="d-flex flex-row">

    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Add Item</h3>
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
                        <label for="type_id">Type</label>
                        <select class="form-control" id="type_id" name="type_id">
                            @foreach($types as $key => $type)
                            <option value="{{$type->id}}">{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
                </form>
            </div>
        </div>
    </div>

    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Add Type</h3>
            <div class="d-flex p-4">
                <form action="{{ route('types.store') }}" method="post">
                    @method('POST')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input class="form-control" id="name" name="name"></input>
                    </div>
                    <button type="submit" class="btn btn-primary">Add</button>
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
                    <th scope="col">Type</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td>{{$item->id}}</th>
                    <td>{{$item->singular}}</th>
                    <td>{{$item->plural}}</th>
                    <td>{{$item->typename}}</th>
                    <td>
                        <div class="d-flex align-items-center">
                            <a class="btn btn-primary me-2 edit" href="{{ route('items.edit', $item->id ) }}">Edit</a>
                            <a class="btn btn-primary me-2 delete" onclick="return confirm('Delete \'{{ $item->singular }}\'?')" href="{{ route('items.destroy', $item->id ) }}">Delete</a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@stop