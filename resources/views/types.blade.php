<x-layout>
    <div class="d-flex flex-row">
        <div class="card m-5">
            <div class="card-body">
                <h3 class="card-title">Typ hinzufügen</h3>
                <div class="d-flex p-4">        
                    <form action="/api/types" method="post">
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
                        <th scope="col">Bearbeiten</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($types as $key => $types)
                        <tr>    
                            <th>{{$types->id}}</th>
                            <th>{{$types->name}}</th>
                            <th><a href="/api/types/{{$types->id}}/edit">Bearbeiten</a></th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>





