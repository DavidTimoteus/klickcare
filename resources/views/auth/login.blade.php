@extends('auth.index')
@section('login')
    <!--Form Login-->
    <main class="content">
        <br>
        <h2 class="relogin fw-bold">Halaman Login</h2>
        <form class="row g-2 mt-2" id="LoginForm" method="POST" action="">
            <section class="col-md-5">
                <div class="row">
                    <article class="col-md-12">
                        <div class="col-md-12 form-register">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text" class="form-control" id="name" name="name" />
                        </div>
                        <div class="col-md-12 form-register">
                            <label for="noUser" class="form-label">NIM/NIP/NIK</label>
                            <input type="text" class="form-control" id="noUser" name="noUser" />
                        </div>
                        <div class="col-md-12 form-register">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" />
                        </div>
                        <div class="col-md-12 form-register">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" />
                        </div>
                    </article>
                </div>
                <div class="row g-2">
                    <div class="col-4">
                        <button type="submit" name="login" class="btn btn-primary btn-login"
                            style="margin: 20px 0px 20px 0px;">Login</button>
                    </div>
                    <div class="col-6" style="margin: 20px 0px 20px 0px;">
                        <p style="margin-bottom: 5px;">Belum punya akun?</p>
                        <a class="btn btn-danger Register-Link" href="">Registrasi</a>
                    </div>
                    <div class="row">
                        <div class="col-12" style="padding-left: 0;">
                            <input type="radio" class="btn-check" name="status" value="3" id="mahasiswa"
                                autocomplete="off" checked />
                            <label class="btn btn-primary btn-status" for="mahasiswa">Mahasiswa</label>

                            <input type="radio" class="btn-check" name="status" value="2" id="dosen"
                                autocomplete="off" />
                            <label class="btn btn-primary btn-status" for="dosen">Dosen/Pegawai</label>

                            <input type="radio" class="btn-check" name="status" value="1" id="admin"
                                autocomplete="off" />
                            <label class="btn btn-primary btn-status" for="admin">Admin</label>
                        </div>
                    </div>
                </div>
            </section>
            <aside class="col-md-7">
                <div>
                    <img src="{{ asset('img/side-login.png') }}" height="420px" alt="Side Login Image" />
                </div>
            </aside>
        </form>
    </main>
    <!--End Form Login-->
@endsection
