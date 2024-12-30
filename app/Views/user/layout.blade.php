@php
use App\Controllers\ServiceController;
$service=new ServiceController();
$token=getCookieValue('login_token');
if ($token!=null){
$user_login=$service->verifyTokenServer($token);
}
@endphp
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
    @section('head')
    <title>{{NAME()}}</title>
    @show
</head>
<body>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.2/jquery.validate.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @include('user.components.navbar')
    @yield('content')
    @include('user.components.footer')
  
   
    @section('scripts')
    <script></script>
    @show
</body>
</html>