<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ env('APP_NAME') }}</title>

    <link href="css/main.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/0c00d2bac9.js" crossorigin="anonymous"></script>
    <script src="js/script.js" defer></script>
</head>

<body>
    @yield('content')
</body>

</html>