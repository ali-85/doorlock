@extends('layouts.default_layout')
@section('title', 'Schedule')
@push('css')
    <link href="{{ asset('assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/sweetalert2/dist/sweetalert2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css') }}">
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
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
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
            ajax: "{{ route('schedule.index') }}",
            processing: true,
            serverSide: true,
            responsive: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'tanggal_awal'
                },
                {
                    data: 'tanggal_akhir'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                "targets": [0, 4],
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
                    extend: 'excel',
                    className: 'btn-sm btn-success text-white',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    exportOptions: {
                        columns: 'th:not(:last-child)'
                    }
                },
                {
                    extend: 'pdf',
                    className: 'btn-sm btn-success text-white',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
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
            url = "{{ route('schedule.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Schedule");
            $('.modal-body form')[0].reset();
            $('#IdDept, #IdLocation, #Type, #TypeAccess, #Priset').selectpicker('val', '');
            $('input[name="_method"]').remove();
            $('#basicModal').modal('show');
        }

        function edit(id) {
            let editUrl = "{{ route('schedule.edit', ':id') }}";
            editUrl = editUrl.replace(':id', id);
            url = "{{ route('schedule.update', ':id') }}";
            url = url.replace(':id', id);
            method = "POST";
            $('.modal-body form').append('<input type="hidden" name="_method" value="PUT" />');
            $.get(editUrl, function(res){
                $('.modal-header h5').html("Edit Schedule");
                $('input[name="nama"]').val(res.data.nama);
                $('input[name="tanggal_awal"]').val(res.data.tanggal_awal);
                $('input[name="tanggal_akhir"]').val(res.data.tanggal_akhir);
                let karyawan = [];
                $.each(res.data.karyawan, function(idx, val){
                    karyawan.push(val.id);
                })
                $('#Employee').selectpicker('val', karyawan);
                let doorlock = [];
                $.each(res.data.doorlock, function(idx, val){
                    doorlock.push(val.id);
                })
                $('#Doorlock').selectpicker('val', doorlock);
                $('#basicModal').modal('show');
            })
        }

        function destroy(id) {
            let deleteUrl = "{{ route('schedule.destroy', ':id') }}";
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
            $('#Awal, #Akhir').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#Employee').selectpicker({
                liveSearch: true,
                header: "Pilih Pegawai"
            });
            $('#Doorlock').selectpicker({
                liveSearch: true,
                header: "Pilih Doorlock"
            });
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Schedule</a></li>
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
                                    <h5 class="card-title">Schedule Table</h5>
                                    <button class="btn btn-primary float-right" onclick="create()"><i class="icon-plus mr-1"></i> Schedule</button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>KEGIATAN</th>
                                                    <th>TANGGAL AWAL</th>
                                                    <th>TANGGAL AKHIR</th>
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
                            <label for="Kegiatan">Kegiatan</label>
                            <input type="text" id="Kegiatan" class="form-control" name="nama" placeholder="Nama Kegiatan" required>
                        </div>
                        <div class="form-group">
                            <label for="Awal">Tanggal Awal</label>
                            <div class="input-group">
                                <input type="text" name="tanggal_awal" class="form-control" id="Awal" placeholder="yyyy-mm-dd" autocomplete="off" required> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Akhir">Tanggal Akhir</label>
                            <div class="input-group">
                                <input type="text" class="form-control" name="tanggal_akhir" id="Akhir" placeholder="yyyy-mm-dd" autocomplete="off" required> <span class="input-group-append"><span class="input-group-text"><i class="mdi mdi-calendar-check"></i></span></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Employee">Pegawai</label>
                            <select name="employee_id[]" id="Employee" class="form-control" data-title="Pilih Pegawai" multiple required>
                                @foreach ($employees as $item)
                                    <option value="{{ $item->id }}">{{ $item->nip.' | '.$item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Doorlock">Remarks</label>
                            <select name="doorlock_id[]" id="Doorlock" class="form-control" data-title="Pilih Doorlock" multiple required>
                                @foreach ($doorlock as $item)
                                    <option value="{{ $item->id }}">{{ $item->uid.' | '.$item->name }}</option>
                                @endforeach
                            </select>
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
