@extends('user.layout')

@section('head')
    <title>{{ NAME() }}</title>
@endsection

@section('content')
    @php
        $parsedDate = date('d-m-Y H:i:s', strtotime($user_login['u_created_at']));
        function getrole($role)
        {
            if ($role == 'user') {
                return 'ผู้ใช้';
            } elseif ($role == 'admin') {
                return 'ผู้ดูแลระบบ';
            } elseif ($role == 'employee') {
                return 'พนักงาน';
            } else {
                return 'ไม่พบข้อมูล';
            }
        }
    @endphp

    <div class="max-w-4xl mx-auto  p-4 bg-white rounded-lg shadow-lg  mt-[80px]">
        <div class="grid grid-cols-1  gap-4">
            <div class="text-center text-2xl font-semibold">Profile</div>
            <div class="w-full flex flex-col items-center">

                <div class="w-full flex flex-col items-center mt-4">
                    <div class="w-full flex justify-between items-center">
                        <div class="w-1/3">Name</div>
                        <div class="w-2/3">: {{ $user_login['u_fullname'] }}</div>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <div class="w-1/3">Email</div>
                        <div class="w-2/3">: {{ $user_login['u_email'] }}</div>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <div class="w-1/3">Role</div>
                        <div class="w-2/3">: {{ getrole(fillterpermission($user_login['u_permission'])) }}</div>
                    </div>
                    <div class="w-full flex justify-between items-center">
                        <div class="w-1/3">Created At</div>
                        <div class="w-2/3">: {{ $parsedDate }}</div>
                    </div>
                    <div class="w-full flex justify-center items-center">
                        <button class="bg-blue-500 text-white px-4 py-2 rounded" onclick="toggleModal()">Change
                            password</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Button to open the modal -->


    <!-- Modal -->
    <div id="myModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 hidden z-50">
        <div class="bg-white p-6 rounded-lg shadow-lg w-1/3">
            <h2 class="text-2xl font-semibold mb-4">Change Password</h2>
            <form id="change_password_form" action="" class="flex flex-col">
                <input type="password" name="password" id="password" class="border p-2 rounded mb-4"
                    placeholder="New Password">
                <input type="password" name="password" id="re_password" class="border p-2 rounded mb-4"
                    placeholder="Confirm Password">
                <button class="bg-blue-500 text-white px-4 py-2 rounded">Change</button>

            </form>
            <br>
            <button class="bg-red-500 text-white px-4 py-2 rounded" onclick="toggleModal()">Close</button>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        function toggleModal() {
            const modal = document.getElementById('myModal');
            modal.classList.toggle('hidden');
        }
        var token = getCookie('login_token');
        $("#change_password_form").submit(function(e) {
            e.preventDefault();


            var password = $("#password").val();
            let re_password = $("#re_password").val();
            if (password != re_password) {
                Swal.fire({
                    title: "Error",
                    text: "Password not match",
                    icon: "error",
                    timer: 5000,
                    timerProgressBar: true,
                });
                return;
            }
            var token = getCookie('login_token');
            $.ajax({
                url: "{{ route('change_password_api') }}",
                type: "POST",
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: {
                    password: password,
                    re_password: re_password
                },
                success: function(response) {
                    if (response.status == 'success') {
                        Swal.fire({
                            title: "success",
                            text: "Change password success",
                            icon: "success",
                            timer: 5000,
                            timerProgressBar: true,
                        }).then(() => {
                            toggleModal();
                        });

                    } else {
                        Swal.fire({
                            title: "Error",
                            text: response.message,
                            icon: "error",
                            timer: 5000,
                            timerProgressBar: true,
                        });
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    Swal.fire({
                        title: "Error",
                        text: jqXHR.responseJSON.message,
                        icon: "error",
                        timer: 5000,
                        timerProgressBar: true,
                    });
                },
                dataType: "json"
            });
        });
    </script>
@endsection
