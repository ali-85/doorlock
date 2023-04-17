@extends('layouts.default_layout')
@section('title', 'Leave and Absence')
@push('css')
    <link href="{{ asset('assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.css') }}">
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
            ajax: "{{ route('leave-absence.index') }}",
            processing: true,
            serverSide: true,
            responsive: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'category'
                },
                {
                    data: 'remark'
                },
                {
                    data: 'value_1A'
                },
                {
                    data: 'value_1B'
                },
                {
                    data: 'value_1C'
                },
                {
                    data: 'value_1D'
                },
                {
                    data: 'value_1E'
                },
                {
                    data: 'value_1F'
                },
                {
                    data: 'value_2A'
                },
                {
                    data: 'value_2B'
                },
                {
                    data: 'value_2C'
                },
                {
                    data: 'value_2D'
                },
                {
                    data: 'value_2E'
                },
                {
                    data: 'value_2F'
                },
                {
                    data: 'value_3A'
                },
                {
                    data: 'value_3B'
                },
                {
                    data: 'value_3C'
                },
                {
                    data: 'value_3D'
                },
                {
                    data: 'value_3E'
                },
                {
                    data: 'value_3F'
                },
                {
                    data: 'value_4A'
                },
                {
                    data: 'value_4B'
                },
                {
                    data: 'value_4C'
                },
                {
                    data: 'value_4D'
                },
                {
                    data: 'value_4E'
                },
                {
                    data: 'value_4F'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                "targets": [0, 27],
                "orderable": false
            },{
                "targets": [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26],
                "visible": false
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
            url = "{{ route('leave-absence.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Leave and Absence");
            $('#basicModal').modal('show');
        }

        function edit(id) {
            let editUrl = "{{ route('leave-absence.edit', ':id') }}";
            editUrl = editUrl.replace(':id', id);
            url = "{{ route('leave-absence.update', ':id') }}";
            url = url.replace(':id', id);
            method = "POST";
            $('.modal-body form').append('<input type="hidden" name="_method" value="PUT" />');
            $.get(editUrl, function(res){
                $('.modal-header h5').html("Edit Leave and Absence");
                $('input[name="remark"]').val(res.data.remark);
                $('select[name="category"]').val(res.data.category);
                $('#1A').val(res.data.value_1A);
                $('#1B').val(res.data.value_1B);
                $('#1C').val(res.data.value_1C);
                $('#1D').val(res.data.value_1D);
                $('#1E').val(res.data.value_1E);
                $('#1F').val(res.data.value_1F);
                $('#2A').val(res.data.value_2A);
                $('#2B').val(res.data.value_2B);
                $('#2C').val(res.data.value_2C);
                $('#2D').val(res.data.value_2D);
                $('#2E').val(res.data.value_2E);
                $('#2F').val(res.data.value_2F);
                $('#3A').val(res.data.value_3A);
                $('#3B').val(res.data.value_3B);
                $('#3C').val(res.data.value_3C);
                $('#3D').val(res.data.value_3D);
                $('#3E').val(res.data.value_3E);
                $('#3F').val(res.data.value_3F);
                $('#4A').val(res.data.value_4A);
                $('#4B').val(res.data.value_4B);
                $('#4C').val(res.data.value_4C);
                $('#4D').val(res.data.value_4D);
                $('#4E').val(res.data.value_4E);
                $('#4F').val(res.data.value_4F);
                $('#basicModal').modal('show');
            })
        }

        function destroy(id) {
            let deleteUrl = "{{ route('leave-absence.destroy', ':id') }}";
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
        $(document).ready(function() {
            $('#basicModal').on('hide.bs.modal', function(){
                $('.modal-body form')[0].reset();
                $('input[name="_method"]').remove();
            })
            $('.modal-body form').on('submit', function(e){
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                formData.append('createdBy', "{{ Auth::user()->name }}");
                formData.append('updatedBy', "{{ Auth::user()->name }}");
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
                        notification(res.responseJSON.status, res.responseJSON.message);
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Leave and Absence</a></li>
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
                                    <h5 class="card-title">Leave and Absence Table</h5>
                                    <button class="btn btn-primary float-right" onclick="create()"><i class="icon-plus mr-1"></i> Leave and Absence</button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>CATEGORY</th>
                                                    <th>KETERANGAN</th>
                                                    @for ($i = 1; $i < 5; $i++)
                                                        @for ($idx = 'A'; $idx < 'G'; $idx++)
                                                            <th>{{ $i.$idx }}</th>
                                                        @endfor
                                                    @endfor
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
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="Remark">Keterangan</label>
                            <input type="text" id="Remark" class="form-control" name="remark" placeholder="Keterangan" required>
                        </div>
                        <div class="form-group">
                            <label for="Category">Kategori</label>
                            <select name="category" id="Category" class="form-control" required>
                                <option disabled selected>Pilih Kategori</option>
                                <option value="salary increase">Penambahan Gaji</option>
                                <option value="payroll deductions">Pengurangan Gaji</option>
                            </select>
                        </div>
                        @for ($i = 1; $i < 5; $i++)
                            @for ($idx = 'A'; $idx < 'G'; $idx++)
                                <div class="form-group">
                                    <label for="{{ $i.$idx }}">Golongan {{ $i.$idx }}</label>
                                    <input type="number" id="{{ $i.$idx }}" class="form-control" name="value_{{ $i.$idx }}" placeholder="Nominal" value="0" required>
                                </div>
                            @endfor
                        @endfor
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
