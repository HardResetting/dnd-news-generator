<x-layout>
<h3>{{ $title ?? 'DND News Generator' }}</h3>
    <table class="table">
        <thead>
            <tr>
            <th scope="col">#</th>
            <th scope="col">Singular</th>
            <th scope="col">Plural</th>
            <th scope="col">Typ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $key => $items)
                <tr>    
                    <th>{{$items->id}}</th>
                    <th>{{$items->singular}}</th>
                    <th>{{$items->plural}}</th>
                    <th>{{$items->type_id}}</th>
                </tr>
            @endforeach
        </tbody>
    </table>

    <form action="{{url('post')}}" method="post">
        <div class="form-group">
            <label for="singular">Singular</label>
            <input class="form-control" id="singular"></input>
        </div>
        <div class="form-group">
            <label for="plural">Plural</label>
            <input class="form-control" id="plural"></input>
        </div>
        <div class="form-group">
            <label for="type_id">Typ</label>
            <select class="form-control" id="type_id">
                @foreach($types as $key => $types)  
                    <option>{{$types->name}}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Hinzufügen</button>
    </form>
</x-layout>





