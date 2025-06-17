<!DOCTYPE html>
<html>

<meta http-equiv="content-type" content="text/html;charset=UTF-8" />

<head>
    <title>Login</title>
    <!-- css -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <meta name="format-detection" content="telephone=no">
    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/backend/auth/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/auth/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/backend/auth/css/slick.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/backend/auth/css/style.css') }}?v={{ filemtime(public_path('assets/backend/auth/css/style.css')) }}">

    <link rel="icon" href="{{ asset('assets/backend/auth/images/cropped-favicon-sgomedia-32x32.png') }}"
        type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('global/css/toastr.css') }}">
</head>
<style type="text/css">
    #toast-container>div {
        width: auto !important;
    }

    .error_txt {
        color: red;
    }

    .active {
        display: none;
    }

    .btn {
        margin-top: 20px;
    }

    .pointer {
        cursor: pointer;
    }

    .g-recaptcha div {
        margin: auto;
    }

    .logo_login img {
        margin-bottom: 20px;
    }

    .loginButton:disabled {
        cursor: no-drop;
    }


    @media (min-width: 768px) {
        .login_page .ct_left {
            min-height: 625px;
        }

        .login_page .ct_right {
            min-height: 625px;
        }

        .add_phone {
            display: block;
            text-align: right;
        }

        .add_phone:first,
        {
        padding: 0px 26px !important;
    }
    }

    @media (min-width: 375px) and (max-width: 550px) {
        .rc-image-tile-33 {
            width: 200%;
            height: 200%;
        }

        .rc-image-tile-44 {
            width: 300%;
            height: 300%;
        }

        .add_phone {
            display: block;
            text-align: right;
            /* padding: 0px 29px; */
        }

        .add_phone:nth-of-type(1),
        {
        padding: 0px 29px;
    }
    }

    .support-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .support-item {
        display: flex;
        /* justify-content: space-between; */
        align-items: center;
        /* margin-bottom: 20px; */
    }

    .diff_strong {
        font-weight: bold;
        color: #fff;
        flex-shrink: 0;
        margin-right: 20px;
    }

    .phone-wrapper {
        display: flex;
        flex-direction: column;
        text-align: right;
        /* Căn phải */
    }

    .phone-wrapper span {
        /* display: flex; */
        justify-content: flex-end;
        /* Căn nội dung số điện thoại và chú thích bên phải */
        align-items: center;
        gap: 10px;
        /* Khoảng cách giữa số và chú thích */
    }

    .normal_strong {
        font-weight: normal;
        color: #fff;
    }

    p {
        margin: 0;
        font-size: 14px;
        color: #ddd;
    }
</style>

<body class="form_page">
    <div id="qb_content_navi_2021">
        <div class="login_display_02 login_page">
            <div class="ct_left">
                <h2 class="title_login">Liên hệ với chúng tôi</h2>
                <div class="ct_left_ct">
                    <ul class="support-list">
                        <li>
                            <div class="support-item">
                                <strong class="diff_strong">Hỗ trợ kỹ thuật:</strong>
                                <div class="phone-wrapper">
                                    <span>
                                        <strong class="normal_strong">(024) 62 927 089</strong>
                                        <p>(24/7)</p>
                                    </span>
                                    <span>
                                        <strong class="normal_strong">0981 185 620</strong>
                                        <p>(24/7)</p>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="support-item">
                                <strong class="diff_strong">Hỗ trợ hoá đơn:</strong>
                                <div class="phone-wrapper">
                                    <span>
                                        <strong class="normal_strong">(024) 62 927 089</strong>
                                        <p>(8h30 - 18h00)</p>
                                    </span>
                                    <span>
                                        <strong class="normal_strong">0912 399 322</strong>
                                        <p>(8h30 - 18h00)</p>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="support-item">
                                <strong class="diff_strong">Hỗ trợ gia hạn:</strong>
                                <div class="phone-wrapper">
                                    <span>
                                        <strong class="normal_strong">(024) 62 927 089</strong>
                                        <p>(8h30 - 18h00)</p>
                                    </span>
                                    <span>
                                        <strong class="normal_strong">0981 185 620</strong>
                                        <p>(8h30 - 18h00)</p>
                                    </span>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="support-item">
                                <strong class="diff_strong">Email:</strong>
                                <span>
                                    <strong class="normal_strong">info@sgomedia.vn</strong>
                                </span>
                            </div>
                        </li>
                    </ul>

                </div>
            </div>
            <div class="ct_right">
                <div class="ct_right_ct">

                    <figure class="logo_login">
                        <a href="https://sgomedia.vn/"><img style="width: 210px !important"
                                src="{{ asset('assets/backend/auth/images/1693475024727-logo-sgo-media-file-chot-1.png') }}"
                                alt="logo-sgo-media"></a>
                    </figure>

                    <div class="login_form">
                        <form id="myForm">
                            @csrf

                            <div class="form_group" style="display: block;">
                                <label for="email" class="form-lable fw-bold">Email</label>
                                <div class="list_group">
                                    <input type="text" name="email" placeholder="Địa chỉ Email" id="email">
                                    <figure class="feild_icon"><img
                                            src="{{ asset('assets/backend/auth/images/login_user_icon.png') }}">
                                    </figure>
                                </div>

                                <label for="password" class="form-lable fw-bold">Mật khẩu</label>
                                <div class="list_group">
                                    <input type="password" name="password" autocomplete="off" placeholder="Password"
                                        id="password">
                                    <figure class="feild_icon">
                                        <img src="{{ asset('assets/backend/auth/images/login_padlock_icon.png') }}">
                                    </figure>
                                    <i class="far fa-eye toggle-password"
                                        style="cursor:pointer; position:absolute; right:10px; top:50%; transform:translateY(-50%);"></i>
                                </div>

                                <div class="form-group">
                                    <div class="form-check my-3">
                                        <input class="form-check-input" name="remember" type="checkbox" id="remember">
                                        <label class="form-check-label" for="remember">
                                            Lưu mật khẩu
                                        </label>
                                    </div>
                                </div>

                                <div class="btn">
                                    <button type="submit" name="button"
                                        class="loginButton loginButtonGg remove-msg before-login disabled_button"
                                        id="submitBtn">Đăng nhập</button>
                                </div>
                            </div>

                        </form>

                    </div>
                </div>
            </div>


        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('library/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('global/js/toastr.js') }}"></script>
    <script src="{{ asset('global/js/helpers.js') }}"></script>
</body>

</html>

<script type="text/javascript">
    $(document).ready(function() {

        $('.toggle-password').click(function() {
            var input = $('#password');
            var icon = $(this);
            if (input.attr('type') === 'password') {
                input.attr('type', 'text');
                icon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                input.attr('type', 'password');
                icon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        submitForm('#myForm', function(response) {
            window.location.href = response.data.redirect
        })
    });
</script>
