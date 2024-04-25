<?php

use Intouch\Framework\Configuration\SystemConfig;
use Intouch\Framework\Environment\Session;

unset(Session::Instance()->usuario);

?>
@@Layout(login)
 
<div class="auth-page">
    <div class="container-fluid p-0">
        <div class="row">
            <div class="col">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-secondary"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
                    <div class="row justify-content-center align-items-center">
                        <div class="col-xl-7">
                            <div class="p-0 p-sm-4 px-xl-0">
                                <div id="reviewcarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                    
                                    <!-- end carouselIndicators -->
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <div class="testi-contain text-white">
                                                <i class="bx bxs-quote-alt-left text-success display-6"></i>

                                                <h4 class="mt-4 fw-medium lh-base text-white">
                                                </h4>
                                                <div class="mt-4 pt-3 pb-5">
                                                    <div class="d-flex align-items-start">
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <!-- end carousel-inner -->
                                </div>
                                <!-- end review carousel -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="auth-full-page-content d-flex p-sm-5 p-4">
                    <div class="w-100">
                        <div class="d-flex flex-column h-100">
                            <div class="mb-4 mb-md-5 text-center">
                                <a href="/" class="d-block auth-logo">
                                    <img src="assets/images/favicon.ico" alt="" height="28"> <span class="logo-txt">Control de Compras y Stock TI</span>
                                </a>
                            </div>
                            <div class="auth-content my-auto">
                                <div class="text-center">
                                    <p class="text-muted mt-2">Inicia sesión</p>
                                </div>
                                <form class="custom-form mt-4 pt-2">
                                    <div class="mb-3">
                                        <label class="form-label">Nombre Usuario</label>
                                        <input type="text" placeholder="" title="Ingrese su nombre de usuario" required value="" name="username" id="username" class="form-control">
                                    </div>
                                    <div class="mb-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <label class="form-label">Contraseña</label>
                                            </div> 
                                        </div>

                                        <div class="input-group auth-pass-inputgroup">
                                            <input type="password" title="Ingrese su contrase�a" placeholder="******" required value="" name="password" id="password" class="form-control">
                                            <button class="btn btn-light ms-0" type="button" id="password-addon"><i class="fa fa-lock"></i></button>
                                        </div>
                                    </div>
                                   
                                    <div class="mb-3">
                                        <button type='button' class="btn btn-primary btn-block" id="btnLogin">Login</button>
                                    </div>
                                </form>

                               

                                <div class="mt-5 text-center">
                                     
                                </div>
                            </div>
                            <div class="mt-4 mt-md-5 text-center">
                                <p class="mb-0">© <script>
                                        document.write(new Date().getFullYear())
                                    </script> Cciti</p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end auth full page content -->
            </div>
            <!-- end col -->
            <div class="col">
                <div class="auth-bg pt-md-5 p-4 d-flex">
                    <div class="bg-overlay bg-secondary"></div>
                    <ul class="bg-bubbles">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <!-- end bubble effect -->
                    <div class="row justify-content-center align-items-center">
                        <div class="col-xl-7">
                            <div class="p-0 p-sm-4 px-xl-0">
                                <div id="reviewcarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                    
                                    <!-- end carouselIndicators -->
                                    <div class="carousel-inner">
                                        <div class="carousel-item active">
                                            <div class="testi-contain text-white">
                                                <i class="bx bxs-quote-alt-left text-success display-6"></i>

                                                <h4 class="mt-4 fw-medium lh-base text-white">
                                                </h4>
                                                <div class="mt-4 pt-3 pb-5">
                                                    <div class="d-flex align-items-start">
                                                    </div>
                                                </div>
                                            </div>
                                        </div> 
                                    </div>
                                    <!-- end carousel-inner -->
                                </div>
                                <!-- end review carousel -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end col -->
        </div>
        <!-- end row -->
    </div>
    <!-- end container fluid -->
</div> 
  


@@IncludeScriptBundle(loginJS)

@@IncludeStyleBundle(loginCSS)

