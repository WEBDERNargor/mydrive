@extends('user.layout')

@section("head")
<title>{{NAME()}}</title>
@endsection

@section('content')



@php
$parsedDate = date('d-m-Y H:i:s', strtotime($user_login['u_created_at']));
function getrole($role){
    if($role=='user'){
        return 'ผู้ใช้';
    }else if($role=='admin'){
        return 'ผู้ดูแลระบบ';
    }else if($role=='employee'){
        return 'พนักงาน';
    }else{
        return 'ไม่พบข้อมูล';
    }
}
@endphp

<div class="max-w-4xl mx-auto  p-4 bg-white rounded-lg shadow-lg  mt-[80px]">
    <div class="grid grid-cols-1  gap-4">
        <div class="text-center text-2xl font-semibold">Profile</div>
        <div class="w-full flex flex-col items-center">
            <div class="w-1/2 h-1/2 bg-gray-300 rounded-full flex justify-center items-center">
                <img src="{{URL()}}/assets/img/user.png" class="w-1/2 h-1/2 rounded-full" alt="">
            </div>
            <div class="w-full flex flex-col items-center mt-4">
                <div class="w-full flex justify-between items-center">
                    <div class="w-1/3">Name</div>
                    <div class="w-2/3">: {{$user_login['u_fullname']}}</div>
                </div>
                <div class="w-full flex justify-between items-center">
                    <div class="w-1/3">Email</div>
                    <div class="w-2/3">: {{$user_login['u_email']}}</div>
                </div>
                <div class="w-full flex justify-between items-center">
                    <div class="w-1/3">Role</div>
                    <div class="w-2/3">: {{getrole(fillterpermission($user_login['u_permission']))}}</div>
                </div>
                <div class="w-full flex justify-between items-center">
                    <div class="w-1/3">Created At</div>
                    <div class="w-2/3">: {{$parsedDate}}</div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
@section('scripts')

@endsection