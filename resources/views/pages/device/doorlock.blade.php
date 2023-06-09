@extends('layouts.default_layout')
@section('title', 'Doorlock Device')
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
            ajax: "{{ route('device.doorlock.index') }}",
            processing: true,
            serverSide: true,
            responsive: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'uid'
                },
                {
                    data: 'namalokasi'
                },
                {
                    data: 'name'
                },
                {
                    data: 'type'
                },
                {
                    data: 'remark'
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
            url = "{{ route('device.doorlock.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Data Lokasi");
            $('#basicModal').modal('show');
        }

        function edit(id) {
            let editUrl = "{{ route('device.doorlock.edit', ':id') }}";
            editUrl = editUrl.replace(':id', id);
            url = "{{ route('device.doorlock.update', ':id') }}";
            url = url.replace(':id', id);
            method = "POST";
            $('.modal-body form').append('<input type="hidden" name="_method" value="PUT" />');
            $.get(editUrl, function(res){
                let remarks = [];
                let listremark = res.data.remarks;
                $('.modal-header h5').html("Edit Data Lokasi");
                $('input[name="uid"]').val(res.data.uid);
                $('input[name="name"]').val(res.data.name);
                $('select#IdDept').selectpicker('val', res.data.departement_id);
                $('select#IdLocation').selectpicker('val', res.data.location_id);
                $('select#Type').selectpicker('val', res.data.type);
                $('select#TypeAccess').selectpicker('val', res.data.access_type);
                $('input#Remark').prop('checked', (res.data.access_mode == 1?true:false));
                $('input#Monitoring').prop('checked', (res.data.intactivemonitoring == 1?true:false));
                $.each(listremark, function(idx, val){
                    remarks.push(val.id);
                })
                showRemark();
                $('select#Priset').selectpicker('val', remarks);
                $('#basicModal').modal('show');
            })
        }

        function destroy(id) {
            let deleteUrl = "{{ route('device.doorlock.destroy', ':id') }}";
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
        function showRemark(){
            let elem = $('input#Remark');
            if (elem.prop('checked')) {
                $('.priset').css('display', 'block');
            } else {
                $('.priset').css('display', 'none');
            }
        }
        $(document).ready(function() {
            $('#basicModal').on('hide.bs.modal', function(){
                $('.modal-body form')[0].reset();
                $('#IdDept, #IdLocation, #Type, #TypeAccess, #Priset').selectpicker('val', '');
                $('input[name="_method"]').remove();
            })
            $('#IdDept').selectpicker({
                liveSearch: true,
                header: "Pilih Departemen"
            });
            $('#IdLocation').selectpicker({
                liveSearch: true,
                header: "Pilih Lokasi"
            });
            $('#Type').selectpicker({
                liveSearch: true,
                header: "Pilih Tipe Ruangan"
            });
            $('#TypeAccess').selectpicker({
                liveSearch: true,
                header: "Pilih Tipe Akses"
            });
            $('#Priset').selectpicker({
                liveSearch: true,
                header: "Pilih Remarks"
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Doorlock</a></li>
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
                                    <h5 class="card-title">Doorlock Table</h5>
                                    <button class="btn btn-primary float-right" onclick="create()"><i class="icon-plus mr-1"></i> Doorlock</button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>LOCATION</th>
                                                    <th>NAMA</th>
                                                    <th>TIPE</th>
                                                    <th>REMARK</th>
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
                            <label>ID Device</label>
                            <input type="text" class="form-control" name="uid" placeholder="ID Device" required>
                        </div>
                        <div class="form-group">
                            <label for="IdLocation">Lokasi</label>
                            <select name="location_id" id="IdLocation" class="form-control" data-title="Pilih Lokasi">
                                @foreach ($locations as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Nama Device</label>
                            <input type="text" class="form-control" name="name" placeholder="Nama Device" required>
                        </div>
                        <div class="form-group">
                            <label for="IdDept">Departemen</label>
                            <select name="departement_id" id="IdDept" class="form-control" data-title="Pilih Departement">
                                @foreach ($depts as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="Type">Type Ruangan</label>
                            <select name="type" id="Type" class="form-control" data-title="Pilih Tipe Ruangan">
                                <option value="public">Public</option>
                                <option value="restricted">Restricted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="TypeAccess">Akses Tipe</label>
                            <select name="access_type" id="TypeAccess" class="form-control" data-title="Pilih Tipe Akses">
                                <option value="in">IN</option>
                                <option value="out">OUT</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                <input type="checkbox" id="Remark" name="access_mode" class="form-check-input" value="1" onchange="showRemark()">Remark</label>
                            </div>
                        </div>
                        <div class="form-group priset" style="display: none;">
                            <label for="Priset">Remarks</label>
                            <select name="priset_id[]" id="Priset" class="form-control" data-title="Pilih Remark" multiple>
                                @foreach ($prisets as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <div class="form-check form-check-inline">
                                <label class="form-check-label">
                                <input type="checkbox" id="Monitoring" name="intactivemonitoring" class="form-check-input" value="1">Aktif Monitoring?</label>
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
