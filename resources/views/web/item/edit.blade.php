@extends('web.layout')

@section('content')
<div class="d-flex flex-row">
    <div class="card m-5">
        <div class="card-body">
            <h3 class="card-title">Item ändern</h3>
            <div class="d-flex p-4 gap-2">
                <form action="{{ route('items.update', $item->id) }}" method="post">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="singular">Singular</label>
                        <input class="form-control" id="singular" name="singular" value="{{ $item->singular }}"></input>
                    </div>
                    <div class="form-group">
                        <label for="plural">Plural</label>
                        <input class="form-control" id="plural" name="plural" value="{{ $item->plural }}"></input>
                    </div>
                    <div class="form-group">
                        <label for="type_id">Type</label>
                        <select class="form-control" id="type_id" name="type_id">
                            @foreach($types as $key => $type)
                            <option value="{{$type->id}}"<?php if ( $type->id  ==  $item->type_id ) echo('selected'); ?>>{{$type->name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    
</script>
@stop