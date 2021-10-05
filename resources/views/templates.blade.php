<x-layout>
    <script>
        function addText(event) {
            var e = document.getElementById("type_id");
            var type_id = e.value;
            document.getElementById("value").value += type_id;
        }
    </script>

    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Template hinzufügen</h3>

            <div class="d-flex mx-auto" style="width: 400px;">
                <select class="form-control" id="type_id" name="type_id">
                    @foreach($types as $key => $type)  
                        <option value="{{$type->id}}">{{$type->name}}</option>
                    @endforeach
                </select>
                <button onclick="addText(event)" class="btn btn-secondary">Typ-ID einfügen</button>
            </div>

            <div class="p-4 gap-2 w-100">
                <form method="post">

                    <div class="form-group">
                        <label for="value">Template</label>
                        <textarea class="form-control" id="value" name="value" x-text="input"></textarea>
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
                        <th scope="col">value</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $key => $templates)
                        <tr>    
                            <th>{{$templates->id}}</th>
                            <th>{{$templates->value}}</th>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-layout>





