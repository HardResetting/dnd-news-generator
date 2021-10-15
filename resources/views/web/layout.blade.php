<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title> @yield('title', 'DND Message Generator') </title>

  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css" integrity="sha512-YWzhKL2whUzgiheMoBFwW8CKV4qpHQAEuvilg9FAn5VJUDwKZZxkJNuGM4XkWuk94WCrrwslk8yWNGmY1EduTA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

  <!-- Custom JS -->
  <script src="{{ asset('js/global.js') }}"></script>

  @stack('head')
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="/">DnD-News-Generator</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link {{ Request::is('items*') ? 'active' : '' }}" {{ Request::is('items*') ? 'aria-current=page' : '' }} href="{{ route('items.index') }}">Items</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('types*') ? 'active' : '' }}" {{ Request::is('types*') ? 'aria-current=page' : '' }} href="{{ route('types.index') }}">Types</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('templates') ? 'active' : '' }}" {{ Request::is('templates') ? 'aria-current=page' : '' }} href="{{ route('templates.index') }}">Templates</a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ Request::is('templates/generate*') ? 'active' : '' }}" {{ Request::is('templates/generate*') ? 'aria-current=page' : '' }} href="{{ route('templates.generate') }}">Generate Messages</a>
          </li>
      </div>
    </div>
  </nav>

  @yield('content')
</body>

</html>