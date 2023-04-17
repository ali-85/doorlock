@extends('layouts.default_layout')
@section('title', 'Accounts')
@push('css')
    <link href="{{ asset('assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/plugins/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons/js/buttons.colVis.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons/js/buttons.flash.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons/js/buttons.html5.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/datatables.net-buttons/js/buttons.print.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/jszip/dist/jszip.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/build/pdfmake.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/pdfmake/build/vfs_fonts.js') }}"></script>
    <script src="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('assets/icons/font-awesome/js/all.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/toastr/js/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
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
        var Table = $('#daTable').DataTable({
            ajax: "{{ route('account.index') }}",
            processing: true,
            serverSide: true,
            responsive: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'role_name'
                },
                {
                    data: 'username'
                },
                {
                    data: 'name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                "targets": [0, 5],
                "orderable": false
            }],
            buttons: [
                {
                    extend: 'copy',
                    className: 'btn-sm btn-success text-white',
                    text: '<i class="fas fa-copy"></i> Copy',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'excelHtml5',
                    className: 'btn-sm btn-success text-white',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'print',
                    className: 'btn-sm btn-success text-white',
                    text: '<i class="fas fa-print"></i> Print',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                }
            ],
        });

        function create() {
            url = "{{ route('account.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Akun");
            $('#basicModal').modal('show');
        }

        function edit(id) {
            let editUrl = "{{ route('account.edit', ':id') }}";
            editUrl = editUrl.replace(':id', id);
            url = "{{ route('account.update', ':id') }}";
            url = url.replace(':id', id);
            method = "POST";
            $('.modal-body form').append('<input type="hidden" name="_method" value="PUT" />');
            $('.password').css('display', 'none');
            $('.password').find('input').prop('required', false);
            $.get(editUrl, function(res){
                $('.modal-header h5').html("Edit Akun");
                let image = "{{ asset('dist/profiles/:profiles') }}";
                image = image.replace(':profiles', res.data.profile_photo);
                $('#IdRole').selectpicker('val', res.data.role_id);
                $('input[name="name"]').val(res.data.name);
                $('input[name="username"]').val(res.data.username);
                $('input[name="email"]').val(res.data.email);
                $('#preview').attr('src', image);
                $('#basicModal').modal('show');
            })
        }

        function destroy(id) {
            let deleteUrl = "{{ route('account.destroy', ':id') }}";
            deleteUrl = deleteUrl.replace(':id', id);
            swal({
                    title: 'Hapus data ini?',
                    text: "Data ini tidak akan bisa dikembalikan!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus!',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#F44336',
                    reverseButtons: true
                }).then((isConfirm) => {
                if (isConfirm.value) {
                    $.ajax({
                        url: deleteUrl,
                        type: "DELETE",
                        dataType: "JSON",
                        success: function(response){
                            notification(response.status, response.message);
                            Table.ajax.reload(null, false);
                        },
                        error: function(res){
                            notification(res.responseJSON.status, res.responseJSON.message);
                        }
                    })
                }
            })
        }

        function notification(status, message) {
            if (status == 'error') {
                toastr.error(message, status.toUpperCase(), {
                    closeButton: 1,
                    showDuration: "300",
                    hideDuration: "1000",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    timeOut: 5e3,
                    showEasing: "swing",
                    hideEasing: "linear"
                });
            } else {
                toastr.success(message, status.toUpperCase(), {
                    closeButton: 1,
                    showDuration: "300",
                    hideDuration: "1000",
                    showMethod: "fadeIn",
                    hideMethod: "fadeOut",
                    timeOut: 5e3,
                    showEasing: "swing",
                    hideEasing: "linear"
                });
            }
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
            $('#IdRole').selectpicker({
                liveSearch: true,
                header: "Pilih Role"
            });
            $('#basicModal').on('hide.bs.modal', function(){
                $('.modal-body form')[0].reset();
                $('#IdRole').selectpicker('val', '');
                $('#preview').attr('src', "{{ asset('dist/profiles/default.jpg') }}");
                $('.password').css('display', 'block');
                $('.password').find('input').prop('required', true);
                $('input[name="_method"]').remove();
            })
            $('#ProfilePhoto').change(function(){
                const file = this.files[0];
                if (file){
                    let reader = new FileReader();
                    reader.onload = function(event){
                        console.log(event.target.result);
                        $('#preview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            $('.modal-body form').on('submit', function(e){
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                $.ajax({
                    url: getUrl(),
                    type: getMethod(),
                    data: formData,
                    contentType: false,
                    processData: false,
                    dataType: "JSON",
                    success: function(response){
                        $('#basicModal').modal('hide');
                        notification(response.status, response.message);
                        Table.ajax.reload(null, false);
                    },
                    error: function(res){
                        let fields = res.responseJSON.fields;
                        $.each(fields, function(i, val){
                            $.each(val, function(idx, value){
                                notification(response.responseJSON.status, value);
                            })
                        })
                    }
                })
            })
        })
    </script>
@endpush
@section('content')
    <!--**********************************
                                Content body start
                            ***********************************-->
    <div class="content-body">

        <div class="row page-titles mx-0">
            <div class="col p-md-0">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="javascript:void(0)">Dashboard</a></li>
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Accounts</a></li>
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <div class="row">
                <div class="col-12 m-b-30">
                    <div class="row">
                        <div class="col">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Akun Table</h5>
                                    <button class="btn btn-primary float-right" onclick="create()"><i class="icon-plus mr-1"></i> Akun</button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>ROLE</th>
                                                    <th>USERNAME</th>
                                                    <th>NAMA</th>
                                                    <th>EMAIL</th>
                                                    <th>AKSI</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Col -->
                    </div>
                </div>
            </div>
        </div>
        <!-- #/ container -->
    </div>
    <!--**********************************
        Content body end
    ***********************************-->
    <!-- Modal -->
    <div class="modal fade" id="basicModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="IdRole">Role</label>
                            <select name="role_id" id="IdRole" class="form-control" data-title="Pilih Role" required>
                                @foreach ($roles as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Name">Nama</label>
                            <input type="text" id="Name" class="form-control" name="name" placeholder="Nama" required>
                        </div>
                        <div class="form-group">
                            <label for="Username">Username</label>
                            <input type="text" id="Username" class="form-control" name="username" placeholder="Username" onkeypress="return event.charCode != 32" required>
                        </div>
                        <div class="form-group">
                            <label for="Email">Email</label>
                            <input type="email" id="Email" class="form-control" name="email" placeholder="akun@csp.com" required>
                        </div>
                        <div class="form-group password">
                            <label for="Password">Password</label>
                            <div class="input-group mb-3">
                                <input type="password" id="Password" class="form-control" name="password" placeholder="******" required>
                                <div class="input-group-append">
                                    <button onclick="showPassword(this)" class="btn btn-outline-dark" type="button"><i class="fa-regular fa-eye-slash"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="ProfilePhoto">Photo Profile</label>
                            <div class="input-group">
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="profile_photo" id="ProfilePhoto">
                                    <label class="custom-file-label">Upload Foto</label>
                                </div>
                            </div>
                        </div>
                        <div class="my-3">
                            <div class="d-flex justify-content-center">
                                <img id="preview" src="{{ asset('dist/profiles/default.jpg') }}" alt="Photo Profile" class="w-50 img-thumbnail">
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-white" data-dismiss="modal">Tutup <i class="fa-solid fa-xmark"></i></button>
                    <button type="submit" class="btn btn-success text-white">Simpan <i class="fa-solid fa-floppy-disk"></i></button>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
