<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title> @yield('title', 'DND Message Generator') </title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
    <div class="navbar-nav">
      <a class="nav-item nav-link {{ Request::is('home*') || Request::is('/') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
      <a class="nav-item nav-link {{ Request::is('items*') ? 'active' : '' }}" href="{{ route('items.index') }}">Items</a>
      <a class="nav-item nav-link {{ Request::is('types*') ? 'active' : '' }}" href="{{ route('types.index') }}">Typen</a>
      <a class="nav-item nav-link {{ Request::is('templates*') ? 'active' : '' }}" href="{{ route('templates.index') }}">Templates</a>      
    </div>
  </div>
</nav>

    @yield('content')
</body>

</html>