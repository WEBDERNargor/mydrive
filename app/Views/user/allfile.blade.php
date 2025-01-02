@extends('user.layout')
@section('top_php')
    @php

    @endphp
@endsection
@section('head')
    <title>Files | {{ NAME() }}</title>
@endsection
@section('content')
    <div class="max-w-4xl mx-auto  p-4 bg-white rounded-lg shadow-lg  mt-[80px]">

        <div id="list_file" class="grid grid-cols-1  gap-4">

            <div class="text-center text-2xl font-semibold">Loading data...</div>

        </div>

    </div>
@endsection
@section('scripts')
    <script>
        var url = "{{ URL() }}";
        var token = getCookie('login_token');
        var data = [];
        $(document).ready(function() {


            get_data();
            renderui();

            setInterval(() => {
                get_data();
            }, 15000);
        });

        function get_data() {
            $.ajax({
                url: "{{ route('getallfile_api') }}",
                type: "POST",
                headers: {
                    'authorization': 'Bearer ' + token
                },
                success: function(response) {
                    if (response.status == 'success') {
                        if (!areArraysEqual(data, response.data)) {
                            data = response.data;
                            renderui();
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    alert("Error");
                },
                dataType: "json"
            });
        }


        function renderui() {
            let html = ``;
            if (data.length > 0) {
                for (let index in data) {
                    html += `
         <div class="flex items-center justify-between p-4 border rounded-lg shadow-sm">
            <div class="flex items-center">
                
                <span class="ml-4  font-semibold text-sm md:text:md xl:text-lg">${data[index].file_name }</span>
            </div>
            <div class="flex gap-2">
                <a target="_new" href="${url}/share/${data[index].file_id}" class="bg-blue-500 text-white px-2 py-1 lg:px-4 lg:py-2 text-sm md:text:md xl:text-lg rounded-lg hover:bg-blue-600  ">Url</a>
                <button data-id="${data[index].file_id}" class="delfile bg-red-500 text-white px-2 py-1 lg:px-4 lg:py-2 text-sm md:text-md xl:text-lg rounded-lg hover:bg-red-600 ">Delete</button>
                <select data-id="${data[index].file_id}" class="file-public-select border-2">
                    <option ${data[index].file_public==0?`selected`:``} value="0">private</option>
                     <option ${data[index].file_public==1?`selected`:``} value="1">public</option>
                    </select>
                </div>
        </div>
        `;
                }
            } else {
                html = `<div class="text-center text-2xl font-semibold">No Data Found</div>`;
            }
            $("#list_file").html(html);
        }
        // <img src="${}" alt="" class="w-16 h-16 object-cover rounded-lg">

        function areArraysEqual(arr1, arr2) {
            if (arr1.length !== arr2.length) return false;

            for (let i = 0; i < arr1.length; i++) {
                if (arr1[i].file_name !== arr2[i].file_name || arr1[i].file_public !== arr2[i].file_public) {
                    return false;
                }
            }

            return true;
        }
        $(document).on('click', '.delfile', function() {
            let id = $(this).data('id');


            Swal.fire({
                title: "Are you sure?",
                text: "You won't be able to revert this!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {


                    $.ajax({
                        url: "{{ route('deletefile_api') }}",
                        type: "DELETE",
                        headers: {
                            'authorization': 'Bearer ' + token
                        },
                        data: {
                            id: id
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire({
                                    title: "success",
                                    text: response.message,
                                    icon: "success",
                                    timer: 2000,
                                    timerProgressBar: true,
                                }).then((result) => {
                                    get_data();
                                });
                            } else {
                                Swal.fire({
                                    title: "Error",
                                    text: response.message,
                                    icon: "error"
                                });
                            }
                        },
                        error: function(jqXHR, textStatus, errorThrown) {
                            Swal.fire({
                                title: "Error",
                                text: jqXHR.responseJSON.message,
                                icon: "error",
                            });
                        },
                        dataType: "json"
                    });



                }
            });




        });

        $(document).on('change', '.file-public-select', function() {
        let id = $(this).data('id');
        let value = $(this).val();

        $.ajax({
            url: "{{ route('updatefilepublic_api') }}",
            type: "PATCH",
            headers: {
                'authorization': 'Bearer ' + token
            },
            data: {
                id: id,
                is_public: value
            },
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        title: "Updated!",
                        text: response.message,
                        icon: "success",
                        timer: 2000,
                        timerProgressBar: true
                    });
                    get_data();
                } else {
                    Swal.fire({
                        title: "Error",
                        text: response.message,
                        icon: "error"
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                Swal.fire({
                    title: "Error",
                    text: jqXHR.responseJSON.message,
                    icon: "error",
                    timer: 5000,
                    timerProgressBar: true
                });
            }
        });
    });
    </script>
@endsection
