@extends('layouts.default_layout')
@section('title', 'Employees')
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
    <script src="{{ asset('assets/js/jquery.qrcode.js') }}"></script>
    <script src="{{ asset('assets/js/qrcode.js') }}"></script>
    <script src="{{ asset('assets/js/html2canvas.js') }}"></script>
    <script src="{{ asset('assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js') }}"></script>
    <script>
        let url = '';
        let method = '';
        let nama_karyawan = '';
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
            ajax: "{{ route('employee.index') }}",
            processing: true,
            serverSide: true,
            responsive: true,
            dom: '<"row"<"col-sm-4"l><"col-sm-5"B><"col-sm-3"fr>>t<"row"<"col-sm-5"i><"col-sm-7"p>>',
            columns: [{
                    data: 'DT_RowIndex'
                },
                {
                    data: 'nip'
                },
                {
                    data: 'rfid_number'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'job_title'
                },
                {
                    data: 'namadepartement'
                },
                {
                    data: 'namasubdepartement'
                },
                {
                    data: 'basic_salary'
                },
                {
                    data: 'noHandphone'
                },
                {
                    data: 'email'
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'action'
                }
            ],
            columnDefs: [{
                "targets": [0, 11],
                "orderable": false
            },
            {
                "targets": [7, 8, 9, 10],
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
            url = "{{ route('employee.store') }}";
            method = 'POST';
            $('.modal-header h5').html("Tambah Pegawai");
            $('#basicModal').modal('show');
        }
        function exportExcel(){
            $('.modal-header h5').html("Export Absensi Pegawai");
            $('#exportModal').modal('show');
        }

        function edit(id) {
            let editUrl = "{{ route('employee.edit', ':id') }}";
            let subdept = '';
            let privileges = [];
            editUrl = editUrl.replace(':id', id);
            url = "{{ route('employee.update', ':id') }}";
            url = url.replace(':id', id);
            method = "POST";
            $('.modal-body form').append('<input type="hidden" name="_method" value="PUT" />');
            $('#qrcodeCanvas').empty();
            $.get(editUrl, function(res){
                $('.modal-header h5').html("Edit Pegawai");
                let doorlock_priv = res.data.doorlock;
                // let image = "{{ asset('dist/profiles/:profiles') }}";
                // image = image.replace(':profiles', res.data.profile_photo);
                nama_karyawan = res.data.nama;
                jQuery('#qrcodeCanvas').qrcode({
                    text	: res.data.nip,
                    render: 'div'
                });
                $.each(doorlock_priv, function(i, val){
                    privileges.push(val.id);
                })
                $('#DoorlockPriv').selectpicker('val', privileges)
                $('input[name="nip"]').val(res.data.nip);
                $('input[name="fingerprint"]').val(res.data.fingerprint);
                $('input[name="rfid_number"]').val(res.data.rfid_number);
                $('input[name="nama"]').val(res.data.nama);
                $('input[name="job_title"]').val(res.data.job_title);
                $('input[name="email"]').val(res.data.email);
                $('input[name="basic_salary"]').val(res.data.basic_salary);
                $('#IdDept').selectpicker('val', res.data.departement_id);
                $('select#Kehadiran').val(res.data.attendance_type);
                $('select#Golongan').val(res.data.golongan_id);
                $('select#Shift').val(res.data.shiftcode_id);
                $('select#Alamat').val(res.data.alamat);
                $('select#Pembayaran').val(res.data.transfer_type);
                $('select#ModePembayaran').selectpicker('val', res.data.payment_mode);
                $('input[name="noHandphone"]').val(res.data.noHandphone);
                if (res.data.intmonitoring) {
                    $('input#Monitoring').prop('checked', true);
                } else {
                    $('input#Monitoring').prop('checked', false);
                }
                subdept = res.data.subdepartement_id;
                showHideBank();
                $('#basicModal').modal('show');
            }).then(() => {
                let wrapper = $('#IdSubdept');
                let option = '';
                wrapper.find('option').remove();
                let optionUrl = "{{ route('employee.subdepartement.option', ':id') }}";
                optionUrl = optionUrl.replace(':id', $('#IdDept').val());
                $.get(optionUrl, function(response){
                    let data = response.data;
                    $.each(data, function(idx, value){
                        option += '<option value="'+value.id+'">'+value.nama+'</option>';
                    })
                    wrapper.append(option).selectpicker('refresh');
                    wrapper.selectpicker('val', subdept);
                })
            })
        }

        function destroy(id) {
            let deleteUrl = "{{ route('employee.destroy', ':id') }}";
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
        function downloadQr(that){
            html2canvas(document.getElementById("qrcodewrapper"),
			{
				allowTaint: true,
				useCORS: true
			}).then(function (canvas) {
				var anchorTag = document.createElement("a");
				document.body.appendChild(anchorTag);
				anchorTag.download = nama_karyawan+"_Qr_Code.jpg";
				anchorTag.href = canvas.toDataURL();
				anchorTag.target = '_blank';
				anchorTag.click();
			});
        }
        function listSubdepartement(that){
            let wrapper = $('#IdSubdept');
            let option = '';
            wrapper.find('option').remove();
            let optionUrl = "{{ route('employee.subdepartement.option', ':id') }}";
            optionUrl = optionUrl.replace(':id', $(that).val());
            $.get(optionUrl, function(response){
                let data = response.data;
                $.each(data, function(idx, value){
                    option += '<option value="'+value.id+'">'+value.nama+'</option>';
                })
                wrapper.append(option).selectpicker('refresh');
            })
        }
        function showHideBank(){
            if ($('select#Pembayaran').val() == '1') {
                $('.norek, .bank, .bank-name').css('display', 'block');
            } else {
                $('.norek, .bank, .bank-name').css('display', 'none');
            }
        }
        $(document).ready(function() {
            $('#IdDept').selectpicker({
                liveSearch: true,
                header: "Pilih Departement",
                title: "Pilih Departement",
            });
            $('#IdSubdept').selectpicker({
                liveSearch: true,
                header: "Pilih Sub Departement",
                title: "Pilih Sub Departement",
            });
            $('#DoorlockPriv').selectpicker({
                liveSearch: true,
                header: "Pilih Doorlock Privileges",
                title: "Pilih Doorlock Privileges",
            });
            $('#Bank').selectpicker({
                liveSearch: true,
                header: "Pilih Bank",
                title: "Pilih Bank",
            });
            $('#ModePembayaran').selectpicker({
                liveSearch: true,
                header: "Pilih Mode Pembayaran",
                title: "Pilih Mode Pembayaran",
            });
            $('.tanggal').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });
            $('#basicModal').on('hide.bs.modal', function(){
                $('.modal-body form')[0].reset();
                $('#IdDept', '#IdSubdept', '#Bank', '#ModePembayaran', '#DoorlockPriv').selectpicker('val', '');
                $('#preview').attr('src', "{{ asset('dist/profiles/default.jpg') }}");
                $('input[name="_method"]').remove();
            })
            $('#ProfilePhoto').change(function(){
                const file = this.files[0];
                if (file){
                    let reader = new FileReader();
                    reader.onload = function(event){
                        $('#preview').attr('src', event.target.result);
                    }
                    reader.readAsDataURL(file);
                }
            });
            $('#FormEmployee').on('submit', function(e){
                e.preventDefault();
                let formData = new FormData($(this)[0]);
                formData.append('payment_mode', 'weekly');
                formData.append('createdBy', '{{ Auth::user()->nama }}');
                formData.append('updatedBy', '{{ Auth::user()->nama }}');
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
                    <li class="breadcrumb-item active"><a href="javascript:void(0)">Employees</a></li>
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
                                    <h5 class="card-title">Pegawai Table</h5>
                                    <button class="btn btn-success text-white float-left" onclick="exportExcel()"><i class="fa-solid fa-file-excel mr-1"></i> Absensi</button>
                                    <button class="btn btn-primary float-right" onclick="create()"><i class="icon-plus mr-1"></i> Pegawai</button>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="daTable">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>NIP</th>
                                                    <th>RFID</th>
                                                    <th>NAMA</th>
                                                    <th>JOB DESC</th>
                                                    <th>DEPT</th>
                                                    <th>SUB DEPT</th>
                                                    <th>SALARY</th>
                                                    <th>NO HP</th>
                                                    <th>EMAIL</th>
                                                    <th>ALAMAT</th>
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
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="" method="post" id="FormEmployee" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col">
                                <div class="my-3">
                                    <div class="d-flex justify-content-center">
                                        <img id="preview" src="{{ asset('dist/profiles/default.jpg') }}" alt="Photo Profile" class="w-50 img-thumbnail">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="ProfilePhoto">Photo Profile</label>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="profile_photo_path" id="ProfilePhoto">
                                            <label class="custom-file-label">Upload Foto</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="my-3">
                                    <div class="d-flex justify-content-center qrcode p-5" id="qrcodewrapper">
                                        <div id="qrcodeCanvas"></div>
                                    </div>
                                    <div class="d-flex justify-content-center">
                                        <button type="button" onclick="downloadQr(this)" class="btn btn-primary btn-sm"><i class="fa-solid fa-download"></i> Download</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="Nip">Nomor Induk Pegawai</label>
                                    <input type="text" id="Nip" class="form-control" name="nip" placeholder="NIP" required>
                                </div>
                                <div class="form-group">
                                    <label for="Rfid">RFID Number</label>
                                    <input type="text" id="Rfid" class="form-control" name="rfid_number" placeholder="RFID" required>
                                </div>
                                <div class="form-group">
                                    <label for="Fingerprint">Fingerprint</label>
                                    <input type="text" id="Fingerprint" class="form-control" name="fingerprint" placeholder="Fingerprint">
                                </div>
                                <div class="form-group">
                                    <label for="Name">Nama</label>
                                    <input type="text" id="Name" class="form-control" name="nama" placeholder="Nama" required>
                                </div>
                                <div class="form-group">
                                    <label for="Job">Job Title</label>
                                    <input type="text" id="Job" class="form-control" name="job_title" placeholder="Job Title" required>
                                </div>
                                <div class="form-group">
                                    <label for="NoHp">No Handphone</label>
                                    <input type="text" id="NoHp" class="form-control" name="noHandphone" placeholder="No Handphone">
                                </div>
                                <div class="form-group">
                                    <label for="Email">Email</label>
                                    <input type="email" id="Email" class="form-control" name="email" placeholder="pegawai@csp.com">
                                </div>
                                <div class="form-group">
                                    <label for="Kehadiran">Kehadiran</label>
                                    <select name="attendances_type" id="Kehadiran" class="form-control" required>
                                        <option selected disabled>Pilih Device Kehadiran</option>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="IdDept">Departement</label>
                                    <select name="departement_id" id="IdDept" class="form-control" onchange="listSubdepartement(this)" required>
                                        @foreach ($depts as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="IdSubdept">Sub Departement</label>
                                    <select name="subdepartement_id" id="IdSubdept" class="form-control" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="Golongan">Golongan</label>
                                    <select name="golongan_id" id="Golongan" class="form-control" required>
                                        <option selected disabled>Pilih Golongan</option>
                                        @foreach ($golongans as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="ModePembayaran">Mode Pembayaran</label>
                                    <select name="payment_mode" id="ModePembayaran" class="form-control" required>
                                        <option value="weekly">Weekly</option>
                                        <option value="monthly">Monthly</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="DoorlockPriv">Doorlock Privileges</label>
                                    <select name="doorlock_id[]" id="DoorlockPriv" class="form-control" multiple required>
                                        @foreach ($doorlock_devices as $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="Shift">Shift</label>
                                    <select name="shiftcode_id" id="Shift" class="form-control" required>
                                        <option selected disabled>Pilih Shift</option>
                                        @foreach ($shifts as $item)
                                            <option value="{{ $item->id }}">{{ $item->shift_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="Alamat">Alamat</label>
                                    <textarea class="form-control" rows="2" id="Alamat" name="alamat"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="Salary">Basic Salary</label>
                                    <input type="number" name="basic_salary" id="Salary" class="form-control" value="0">
                                </div>
                                <div class="form-group">
                                    <label for="Pembayaran">Pembayaran</label>
                                    <select name="transfer_type" id="Pembayaran" class="form-control" onchange="showHideBank()" required>
                                        <option selected disabled>Pilih Tipe Pembayaran</option>
                                        <option value="1">Bank</option>
                                        <option value="2">Cash</option>
                                    </select>
                                </div>
                                <div class="form-group bank-name" style="display: none;">
                                    <label for="BankName">Nama Pemilik</label>
                                    <input type="text" name="bank_name" id="BankName" class="form-control">
                                </div>
                                <div class="form-group bank" style="display: none;">
                                    <label for="Bank">Bank</label>
                                    <label for="Bank">Pembayaran</label>
                                    <select name="bank_account" id="Bank" class="form-control">
                                        @foreach ($banks as $item)
                                            <option value="{{ $item->id }}">{{ $item->nama_bank.' | '.$item->kode_bank }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group norek" style="display: none;">
                                    <label for="Norek">No Rekening</label>
                                    <input type="number" name="credited_accont" id="Norek" class="form-control">
                                </div>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" id="Monitoring" name="intmonitoring" class="form-check-input" value="1"/>
                                        <label class="form-check-label">Aktif Monitoring?</label>
                                    </div>
                                </div>
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
    <!-- Modal -->
    <div class="modal fade" id="exportModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Modal</h5>
                    <button type="button" class="close" data-dismiss="modal"><span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{ route('absensi.karyawan.excel') }}" method="post" id="FormExport" target="_new">
                        @csrf
                        <div class="form-group">
                            <label for="Awal">Tanggal Awal</label>
                            <input type="text" id="Awal" class="form-control tanggal" name="start" placeholder="Tanggal Awal" autocomplete="off" required>
                        </div>
                        <div class="form-group">
                            <label for="Akhir">Tanggal Akhir</label>
                            <input type="text" id="Akhir" class="form-control tanggal" name="akhir" placeholder="Tanggal Akhir" autocomplete="off" required>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary text-white" data-dismiss="modal">Tutup <i class="fa-solid fa-xmark"></i></button>
                    <button type="submit" class="btn btn-success text-white">Export <i class="fa-solid fa-file-excel"></i></button>
                </form>
                </div>
            </div>
        </div>
    </div>
@endsection
