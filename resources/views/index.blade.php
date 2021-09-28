@extends('default')

@section('content')
  <div>
      <p>
          Testing blade: "{{ $variable }}"
      </p>
      <p>
          We're on version: {{ $app->version() }}
      </p>
    </div>
@endsection

