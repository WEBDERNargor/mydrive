@yield('top_php')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {}
      }
    }
    </script>
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    @section('head')
    <title>{{NAME()}}</title>
    @show
</head>
<body>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    @include('user.components.navbar')
    @yield('content')
    @include('user.components.footer')
  
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
    @section('scripts')
    <script></script>
    @show
</body>
</html>