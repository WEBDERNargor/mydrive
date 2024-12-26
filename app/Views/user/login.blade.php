
@extends('user.layout')

@section('head')
    <title>Login - {{NAME()}}</title>
@endsection
@section('content')

<div class="w-screen h-screen relative">
<div class="absolute w-[85%] lg:w-[500px] h-[400px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 shadow-lg rounded-xl">
    <div class="w-full h-full bg-white rounded-xl flex flex-col justify-center items-center px-4">
        <h1 class="text-2xl font-bold">Login</h1>
        <form id="login_form" action="" method="POST" class="w-full flex flex-col items-center">
            
            <input type="email" name="email" placeholder="Email" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <input type="password" name="password" placeholder="Password" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <button type="submit" class="w-3/4 h-10 bg-blue-500 text-white rounded-xl mt-4 hover:bg-blue-700">Login</button>
            <hr>
            <a href="#" class="w-3/4 h-10 bg-red-500 text-white rounded-xl mt-4 hover:bg-red-700 static flex justify-center items-center">Google</a>
            <a href="{{route('register')}}" class=" text-blue-500">Register</a>
        </form>
    </div>
    
</div>
</div>
@endsection

@section("scripts")
<script>
   $("#login_form").submit(function(e){
       e.preventDefault();
       let data = $(this).serialize();
       $.ajax({
           url: "{{route('login_api')}}",
           type: "POST",
           data: data,
           success: function(response){
               if(response.status == "success"){
                    setCookie("login_token", response.token, 1);
                    // console.log(response.token);
                   window.location.href = "{{route('home')}}";
               }else{
                   alert(response.message);
               }
           }
       });
   });

</script>
@endsection