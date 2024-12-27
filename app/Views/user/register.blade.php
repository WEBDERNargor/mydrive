
@extends('user.layout')

@section('head')
    <title>Register - {{NAME()}}</title>
@endsection
@section('content')
<div class="w-screen h-screen relative">
<div class="absolute w-[85%] lg:w-[500px] top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 shadow-lg rounded-xl">
    <div class="w-full h-auto bg-white rounded-xl flex flex-col justify-center items-center py-4 ">
        <h1 class="text-2xl font-bold">Login</h1>
        <form id="register_form" action="" method="POST" class="w-full flex flex-col items-center">
            <input type="text" id="fname" name="fname" placeholder="First name" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <input type="text"  id="lname" name="lname" placeholder="Last name" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <input type="email" id="email" name="email" placeholder="Email" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <input type="password" id="password" name="password" placeholder="Password" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            <input type="password" id="repass" name="repass" placeholder="Re-password" class="w-3/4 h-10 border-2 border-gray-300 rounded-xl mt-4 px-4">
            
            <button type="submit" class="w-3/4 h-10 bg-blue-500 text-white rounded-xl mt-4 hover:bg-blue-700">Register</button>
            <hr>
            <a href="#" class="w-3/4 h-10 bg-red-500 text-white rounded-xl mt-4 hover:bg-red-700 static flex justify-center items-center">Google</a>
            <a href="{{route('login')}}" class="   text-blue-500">Login</a>
        </form>
       
    </div>
   
</div>
</div>
@endsection

@section("scripts")
<script>
   $("#register_form").submit(function(e){
       e.preventDefault();
       let fname=$("#fname").val();
       let lname=$("#lname").val();
       let email=$("#email").val();
       let password=$("#password").val();
       let repass=$("#repass").val();
         if(password != repass){
              alert("Password and Re-password must be same");
              return;
         }
       $.ajax({
           url: "{{route('register_api')}}",
           type: "POST",
             contentType: "application/json",
            dataType: "json",
           data: JSON.stringify({
               fname: fname,
               lname: lname,
               email: email,
               password: password
           }),
           success: function(response){
               if(response.status == "success"){
                   window.location.href = "{{route('login')}}";
               }else{
                   alert(response.message);
               }
           },
            error: function(xhr, status, error) {
               // จัดการกับ error status code 400
               if (xhr.status === 400) {
                   let response = JSON.parse(xhr.responseText);
                   Swal.fire({
                       icon: 'error',
                       title: 'เกิดข้อผิดพลาด',
                       text: response.message || 'ข้อมูลไม่ถูกต้อง กรุณาตรวจสอบอีกครั้ง'
                   });
               } else {
                   Swal.fire({
                       icon: 'error',
                       title: 'เกิดข้อผิดพลาด',
                       text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง'
                   });
               }
           }
       }
    );
   });

</script>
@endsection