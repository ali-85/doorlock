@extends('layouts.default_layout')
@section('title', 'Report Absence')
@push('css')
    <link href="{{ asset('assets/plugins/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('assets/plugins/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('assets/plugins/bootstrap-material-datetimepicker/css/bootstrap-material-datetimepicker.css') }}">
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
    <script src="{{ asset('assets/plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') }}">
    </script>
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
            ajax: {
                url: "{{ route('absence.index') }}",
                type: "GET",
                data: function(data) {
                    data.awal = $('input[name="start"]').val();
                    data.akhir = $('input[name="finish"]').val();
                }
            },
            processing: true,
            serverSide: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'nama'
                },
                {
                    data: 'jam_masuk'
                },
                {
                    data: 'jam_keluar'
                },
                {
                    data: 'keterangan'
                },
                {
                    data: 'keterangan_detail'
                },
                {
                    data: 'action'
                },
            ],
            columnDefs: [{
                "targets": [0, 5],
                "orderable": false
            }, {
                "targets": [4],
                "visible": false
            }],
            buttons: [{
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

        function setMinDate(that) {
            $('input[name="finish"]').bootstrapMaterialDatePicker('setMinDate', $(that).val());
        }

        function filter() {
            Table.ajax.reload(null, false);
        }

        function create() {
            url = "{{ route('leave-absence.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Report Absence");
            $('#basicModal').modal('show');
        }

        function show(id) {
            let editUrl = "{{ route('absence.show', ':id') }}";
            editUrl = editUrl.replace(':id', id);
            $.get(editUrl, function(res) {
                let data = res.data;
                $('input#Nama').val(data.nama);
                $('input#JamMasuk').val(data.jam_masuk);
                $('input#JamKeluar').val(data.jam_Keluar != null ? data.jam_Keluar : 'Belum keluar');
                $('input#Keterangan').val(data.keterangan);
                $('input#KeteranganDetail').val(data.keterangan_detail);
                $('img#absence-photo').attr('src', '{!! asset("'+data.jam_masuk_photo_path+'") !!}');
                $('img#out-photo').attr('src', '{!! asset("'+data.jam_Keluar_photo_path+'") !!}');
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
                        success: function(response) {
                            notification(response.status, response.message);
                            Table.ajax.reload(null, false);
                        },
                        error: function(res) {
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
            $('#basicModal').on('hide.bs.modal', function() {
                $('.modal-body form')[0].reset();
                $('input[name="_method"]').remove();
            })
            $('input[name="start"], input[name="finish"]').bootstrapMaterialDatePicker({
                weekStart: 0,
                time: false
            });
            $('.modal-body form').on('submit', function(e) {
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
                    success: function(response) {
                        $('#basicModal').modal('hide');
                        notification(response.status, response.message);
                        Table.ajax.reload(null, false);
                    },
                    error: function(res) {
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Report Absence</a></li>
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
                                    <h5 class="card-title">Report Absence Table</h5>
                                    <div class="basic-form">
                                        <div class="form-row align-items-center">
                                            <div class="col-2">
                                                <input class="form-control" type="text" name="start" autocomplete="off"
                                                    placeholder="Start Date" onchange="setMinDate(this)">
                                            </div>
                                            <div class="col-2">
                                                <input class="form-control" type="text" name="finish" autocomplete="off"
                                                    placeholder="Last Date">
                                            </div>
                                            <div class="col-2">
                                                <button onclick="filter()" class="btn btn-primary"><i
                                                        class="fas fa-filter"></i> Filter</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>NAMA</th>
                                                    <th>JAM MASUK</th>
                                                    <th>JAM KELUAR</th>
                                                    <th>STATUS</th>
                                                    <th>KETERANGAN</th>
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
                    <h5 class="modal-title">Detail Absence</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="Nama">Nama :</label>
                            <input type="text" name="txtNama" id="Nama" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="JamMasuk">Jam Masuk :</label>
                            <input type="text" name="jam_masuk" id="JamMasuk" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="JamKeluar">Jam Keluar :</label>
                            <input type="text" name="jam_Keluar" id="JamKeluar" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="Keterangan">Keterangan :</label>
                            <input type="text" name="txtKeterangan" id="Keterangan" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label for="KeteranganDetail">Keterangan Detail :</label>
                            <input type="text" name="txtKeteranganDetail" id="KeteranganDetail" class="form-control"
                                readonly>
                        </div>
                        <div class="form-group">
                            <label for="absence-photo">Foto Masuk :</label>
                            <img src="" id="absence-photo" class="img-thumbnail">
                        </div>
                        <div class="form-group">
                            <label for="out-photo">Foto Pulang :</label>
                            <img src="" id="out-photo" class="img-thumbnail">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-white" data-dismiss="modal">Tutup <i
                            class="fa-solid fa-xmark"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
