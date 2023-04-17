@extends('layouts.default_layout')
@section('title', 'Dashboard')
@push('css')
    <link rel="stylesheet" href="{{ asset('assets/plugins/toastr/css/toastr.min.css') }}">
@endpush
@push('scripts')
    <script src="{{ asset('assets/plugins/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/plugins/toastr/js/toastr.min.js') }}"></script>
    <script>
        let recapotulationctx = document.getElementById("recapotulation-chart").getContext('2d');
        let recapotulationChart = new Chart(recapotulationctx, {
            type: "line",
            data: {
                labels: @json($month),
                datasets: [{
                        label: "Hadir",
                        data: @json($lineH),
                        borderWidth: 2,
                        borderColor: "#3160D8",
                        backgroundColor: "transparent",
                    },
                    {
                        label: "terlambat",
                        data: @json($lineT),
                        borderWidth: 2,
                        borderColor: "#FF8B26",
                        backgroundColor: "transparent",
                    },
                    {
                        label: "Tidak Masuk",
                        data: @json($lineTH),
                        borderWidth: 2,
                        borderColor: "#D32929",
                        backgroundColor: "transparent",
                    },
                ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        ticks: {
                            fontSize: "12",
                            fontColor: "#777777",
                        },
                        gridLines: {
                            display: false,
                        },
                    },
                    y: {
                        ticks: {
                            fontSize: "12",
                            fontColor: "#777777",
                        },
                        gridLines: {
                            color: "#D8D8D8",
                            zeroLineColor: "#D8D8D8",
                            borderDash: [2, 2],
                            zeroLineBorderDash: [2, 2],
                            drawBorder: false,
                        },
                    },
                },
            },
        });

        let todayAttdctx = document.getElementById('today-attd-chart').getContext("2d");
        let todayAttdChart = new Chart(todayAttdctx, {
            type: "doughnut",
            data: {
                labels: ["Tidak hadir", "Terlambat", "Hadir"],
                datasets: [{
                    data: @json($dataTodayChart),
                    backgroundColor: ["#D32929", "#FF8B26", "#91C714", "#285FD3"],
                    hoverBackgroundColor: ["#D32929", "#FF8B26", "#91C714", "#285FD3"],
                    borderWidth: 5,
                    borderColor: "#fff",
                }, ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                    title: {
                        display: false,
                    }
                }
                // cutoutPercentage: 80,
            },
        });

        let timeroomoutctx = document.getElementById("timeroomout-chart").getContext('2d');
        let timeroomoutChart = new Chart(timeroomoutctx, {
            type: "bar",
            data: {
                labels: @json($name),
                datasets: [{
                    label: "Durasi (menit)",
                    data: @json($minutesout),
                    borderWidth: 2,
                    backgroundColor: "#FFC300",
                }, ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                    },
                },
            },
        });
        let frekuensioutctx = document.getElementById("frekuensiout-chart").getContext('2d');
        let frekuensiChart = new Chart(frekuensioutctx, {
            type: "bar",
            data: {
                labels: @json($nameFrekuen),
                datasets: [{
                    label: "Frekuensi",
                    data: @json($frekuensi),
                    borderWidth: 2,
                    backgroundColor: "#3160D8",
                }, ],
            },
            options: {
                plugins: {
                    legend: {
                        display: false,
                    },
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                    },
                },
            },
        });

        function notification(status, message){
            if (status == 'error') {
                toastr.error(message, status.toUpperCase(), {
                    closeButton:1, showDuration:"300", hideDuration:"1000", showMethod:"fadeIn", hideMethod:"fadeOut", timeOut:5e3,
                    showEasing:"swing",hideEasing:"linear"
                });
            } else {
                toastr.success(message, status.toUpperCase(), {
                    closeButton:1, showDuration:"300", hideDuration:"1000", showMethod:"fadeIn", hideMethod:"fadeOut", timeOut:5e3,
                    showEasing:"swing",hideEasing:"linear"
                });
            }
        }
        $(document).ready(function(){
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
                </ol>
            </div>
        </div>
        <!-- row -->

        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-1">
                        <div class="card-body">
                            <h3 class="card-title text-white">Total Employees</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{ $total_employee }}</h2>
                                <p class="text-white mb-0">Peoples</p>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="icon-people"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-2">
                        <div class="card-body">
                            <h3 class="card-title text-white">Employees Present</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{ $employee_presents }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="icon-user-following"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-3">
                        <div class="card-body">
                            <h3 class="card-title text-white">Employees Absence</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{ $employee_absences }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-user-times"
                                    aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6">
                    <div class="card gradient-4">
                        <div class="card-body">
                            <h3 class="card-title text-white">Doorlock Devices</h3>
                            <div class="d-inline-block">
                                <h2 class="text-white">{{ $total_doorlock }}</h2>
                            </div>
                            <span class="float-right display-5 opacity-5"><i class="fa fa-microchip"
                                    aria-hidden="true"></i></span>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Start Row -->
            <div class="row">
                <div class="col-12 m-b-30">
                    <h4 class="d-inline">Attendance Recapitulation</h4>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img1.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Chart Attendances</h5>
                                    <canvas id="recapotulation-chart" height="169" class="mt-6"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- End Col -->
                        <div class="col-md-4">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img2.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Today Attendances</h5>
                                    <canvas class="my-2" id="today-attd-chart" height="100"></canvas>
                                    <div class="basic-list-group">
                                        <ul class="list-group">
                                            <li class="list-group-item list-group-item-danger">Tidak Hadir
                                                {{ $tidakHadirPer }}%</li>
                                            <li class="list-group-item list-group-item-warning">Terlambat
                                                {{ $terlamabatPer }}%</li>
                                            <li class="list-group-item list-group-item-info">Hadir {{ $hadiranPer }}%</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
            <!-- Start Row -->
            <div class="row">
                <div class="col-12 m-b-30">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img1.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Out Monitoring (Durasi)</h5>
                                    <canvas id="timeroomout-chart" height="169" class="mt-6"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- End Col -->
                        <div class="col-md-4">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img2.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Top 10 Out Room (durasi)</h5>
                                    <div class="basic-list-group">
                                        <ul class="list-group">
                                            @foreach ($topdurasi as $item)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $loop->iteration . '. ' . $item->nama }} <span
                                                        class="badge badge-primary">{{ $item->minutes }} Menit</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
            <!-- Start Row -->
            <div class="row">
                <div class="col-12 m-b-30">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img1.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Out Monitoring (frekuensi)</h5>
                                    <canvas id="frekuensiout-chart" height="169" class="mt-6"></canvas>
                                </div>
                            </div>
                        </div>
                        <!-- End Col -->
                        <div class="col-md-4">
                            <div class="card">
                                <img class="img-fluid" src="images/big/img2.jpg" alt="">
                                <div class="card-body">
                                    <h5 class="card-title">Top 10 Out Room (frekuensi)</h5>
                                    <div class="basic-list-group">
                                        <ul class="list-group">
                                            @foreach ($topfrekuensi as $item)
                                                <li
                                                    class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $loop->iteration . '. ' . $item->nama }} <span
                                                        class="badge badge-primary">{{ $item->frekuensi }}x</span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- #/ container -->
    </div>
    <!--**********************************
                            Content body end
                        ***********************************-->
@endsection
