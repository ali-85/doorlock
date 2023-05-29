@extends('layouts.default_layout')
@section('title', 'Profile')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/icons/font-awesome/js/all.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/toastr/js/toastr.min.js') }}"></script>
    <script>
        let url = '';
        let method = '';
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function getUrl() {
            return url;
        }

        function getMethod() {
            return method;
        }
        function showPassword(that){
            let type = $(that).closest('.input-group').find('input').attr('type');
            if (type == 'password') {
                $(that).closest('.input-group').find('input').attr('type', 'text');
                $(that).html('<i class="fa-regular fa-eye"></i>');
            } else {
                $(that).closest('.input-group').find('input').attr('type', 'password');
                $(that).html('<i class="fa-regular fa-eye-slash"></i>');
            }
        }
        $(document).ready(function() {
        })
    </script>
@endpush
@section('content')
<div class="content-body">
    <div class="row page-titles mx-0">
        <div class="col p-md-0">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                <li class="breadcrumb-item active"><a href="javascript:void(0)">Profile</a></li>
            </ol>
        </div>
    </div>
    <!-- row -->

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-xl-3">
                <div class="card">
                    <div class="card-body">
                        <div class="media align-items-center mb-4">
                            <img class="mr-3" src="{{ asset('dist/profiles/'.$user->profile_photo) }}" width="80" height="80" alt="">
                            <div class="media-body">
                                <h3 class="mb-0">{{ $user->name }}</h3>
                                <p class="text-muted mb-0">{{ $user->username }}</p>
                            </div>
                        </div>
                        <ul class="card-profile__info">
                            <li class="mb-1"><strong class="text-dark mr-2">Email</strong> <span>{{ $user->email }}</span></li>
                            <li class="mb-1"><strong class="text-dark mr-2">Role</strong> <span>{{ $user->role_name }}</span></li>
                        </ul>
                        <div class="row mb-5">
                            <div class="col-12 text-center">
                                <button class="btn btn-primary px-5">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8 col-xl-9">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Reset Password</h4>
                        <div class="basic-form">
                            <form>
                                <div class="form-group">
                                    <label for="CurrentPassword">Current Password</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" id="CurrentPassword" placeholder="Current Password">
                                        <div class="input-group-append">
                                            <button onclick="showPassword(this)" class="btn btn-outline-dark" type="button"><i class="fa-solid fa-eye-slash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="NewPassword">New Password</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" id="NewPassword" placeholder="New Password">
                                        <div class="input-group-append">
                                            <button onclick="showPassword(this)" class="btn btn-outline-dark" type="button"><i class="fa-solid fa-eye-slash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ConfirmPassword">Confirm Password</label>
                                    <div class="input-group mb-3">
                                        <input type="password" class="form-control" id="ConfirmPassword" placeholder="Confirm Password">
                                        <div class="input-group-append">
                                            <button onclick="showPassword(this)" class="btn btn-outline-dark" type="button"><i class="fa-solid fa-eye-slash"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success text-white float-right"><i class="far fa-save"></i> Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- #/ container -->
</div>
@endsection
