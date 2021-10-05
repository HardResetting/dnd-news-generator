<x-layout>
    <div class="d-flex flex-row">
        <div class="d-flex p-2">
            <form method="post">
                <h3>Item hinzufügen</h3>
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

        <div class="d-flex p-2">        
            <form action="/api/types" method="post">
            <h3>Typ hinzufügen</h3>
                <div class="form-group">
                    <label for="name">Name</label>
                    <input class="form-control" id="name" name="name"></input>
                </div>            
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>

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
</x-layout>





