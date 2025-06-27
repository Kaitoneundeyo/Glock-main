<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title>Login - Raymona</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/eca8c42def.js" crossorigin="anonymous"></script>

    <!-- Stisla CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components.css') }}">
</head>

<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row justify-content-center">
                    <div class="col-md-6 col-lg-4">
                        <div class="text-center mb-4">
                            <h1 style="font-size: 2rem; font-weight: 700; color: #4e73df;">
                                Raymona<span style="color: #e74c3c;">.</span>
                            </h1>
                            <p class="text-muted" style="font-family: 'Inter', sans-serif;">Belanja Online Frozen Food
                                Termurah se-Banjarmasin</p>
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                            </div>

                            <div class="card-body">
                                <form method="POST" action="{{ route('login-proses') }}" class="needs-validation"
                                    novalidate>
                                    @csrf
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input id="email" type="email" name="email" class="form-control" required
                                            autofocus>
                                        <div class="invalid-feedback">Harap isi email anda</div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password" class="d-block">Password</label>
                                        <div class="input-group">
                                            <input id="password" type="password" name="password" class="form-control"
                                                required>
                                            <div class="input-group-append">
                                                <div class="input-group-text" id="togglePassword"
                                                    style="cursor: pointer;">
                                                    <i class="fas fa-eye" id="passwordIcon"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="invalid-feedback">Harap isi password anda</div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block">Login</button>
                                    </div>

                                    <div class="text-center my-3">ATAU</div>

                                    <div class="form-group text-center">
                                        <a href="{{ route('login.google') }}" class="btn btn-danger btn-block">
                                            <i class="fab fa-google"></i> Login dengan Google
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="simple-footer text-center mt-3">
                            &copy; Raymona {{ date('Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Toggle show/hide password
        $('#togglePassword').on('click', function () {
            const passwordField = $('#password');
            const type = passwordField.attr('type') === 'password' ? 'text' : 'password';
            passwordField.attr('type', type);
            $('#passwordIcon').toggleClass('fa-eye fa-eye-slash');
        });
    </script>
</body>

</html>
