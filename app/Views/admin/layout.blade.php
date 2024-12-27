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

    @section('head')
    <title>{{NAME()}}</title>
    @show
</head>
<body>
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('admin.components.navbar')
    @yield('content')
    @include('admin.components.footer')
  
    @section('scripts')
    <script></script>
    @show
</body>
</html>