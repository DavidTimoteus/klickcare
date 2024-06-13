<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="icon" href="{{ asset('img/klickCare-logo.png') }}" type="image/x-icon" id="KlickCare-Logo">
    <title>KlickCare</title>
    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    @vite(['resources/css/app.css'])
    @vite('resources/css/navfot.css')
</head>

<body>
    @include('components.navbar')
    <div class="lp-content">
        <!-- Hero -->
        <div class="hero" style="background-image: url('{{ asset('img/beranda-slide.png') }}')">
            <div class="row justify-content-evenly">
                <div class="col-6" style="margin-top: 20vh">
                    <h3>Kampus Sehat, Mahasiswa Unggul !</h3>
                    <h1 style="font-weight: 700;">
                        Poliklinik
                        <br />
                        Politeknik Negeri Malang
                    </h1>
                    <p>
                        Menjadi pusat fasilitas kesehatan di lingkungan pendidikan, yang memberikan
                        layanan kesehatan bermutu tinggi
                    </p>
                    <a href="#" class="btn btn-light">Hubungi Kami</a>
                </div>
                <div id="rectangle"></div>
                <img src="{{ asset('img/beranda.png') }}" class="col-4" alt="Healthcare Services" />
            </div>
        </div>
        <!-- End Hero -->

        <!-- Card Layanan Kami -->
        <div class="container" id="layanan">
            <div class="row mt-5">
                <h2>Layanan Kami</h2>
                <div class="col-sm-3 mb-3 mb-sm-0">
                    <a href="{{ url('poliumum') }}">
                        <div class="card text-center">
                            <img src="{{ asset('img/icon/Poli Umum.png') }}" class="card-img-top" alt="Poli Umum" />
                            <div class="card-body">
                                <h5 class="card-title">Poli Umum</h5>
                                <p class="card-text">
                                    Merupakan tempat pelayanan yang bertugas melakukan penanganan
                                    dan perawatan medis terhadap pasien.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 mb-3 mb-sm-0">
                    <a href="">
                        <div class="card text-center">
                            <img src="{{ asset('img/icon/Poli Gizi.png') }}" class="card-img-top" alt="Poli Gizi" />
                            <div class="card-body">
                                <h5 class="card-title">Poli Gizi</h5>
                                <p class="card-text">
                                    Merupakan tempat memberikan saran dan informasi kepada pasien
                                    tentang penatalaksanaan gizi dan masalah kesehatan.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 mb-3 mb-sm-0">
                    <a href="">
                        <div class="card text-center">
                            <img src="{{ asset('img/icon/Poli Mata.png') }}" class="card-img-top" alt="Poli Mata" />
                            <div class="card-body">
                                <h5 class="card-title">Poli Mata</h5>
                                <p class="card-text">
                                    Merupakan tempat pelayanan yang memberikan pemeriksaan dan
                                    perawatan yang berhubungan dengan penyakit mata
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-3 mb-3 mb-sm-0">
                    <a href="">
                        <div class="card text-center">
                            <img src="{{ asset('img/icon/Poli Gigi.png') }}" class="card-img-top" alt="Poli Gigi" />
                            <div class="card-body">
                                <h5 class="card-title">Poli Gigi</h5>
                                <p class="card-text">
                                    Merupakan tempat yang khusus menyediakan layanan perawatan
                                    gigi dan mulut.
                                </p>
                            </div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
