<?php
if(session_status() == PHP_SESSION_NONE): session_start(); endif;
if (isset($_SESSION['user_id'])){
    header("Location: ./");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>login</title>
    <!-- <base href="/proj/docs_verify_portal/"> -->

    <link rel="preload" href="assets/css/base.css" as="style" onload="this.onload=null; this.rel='stylesheet'">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    <style>
    .loginContainer {
        width: 25rem;
        padding: 2.5rem;

        h2 {
            text-align: center;
            font-size: 1.8em;
            font-weight: 500;
        }

        form {
            margin: 2rem 0;

            .captcha-img {
                width: 100%;
                height: 2.3rem;
                border: 1px solid green;
                cursor: pointer;
            }

            .alertBox {
                border: 1px solid;
                padding: 0.3rem 0.8rem;
                border-radius: 5px;
                font-weight: 500;

                &.error {
                    border-color: #db0303;
                    background: #ffcece;
                    color: #58151c;
                }

                &.success {
                    border-color: #05b512;
                    background: #a6f5b4;
                    color: #285815;
                }
            }
        }
    }
    </style>
</head>

<body class="d-flex vh-100">
    <main class="d-flex d-center">
        <div class="loginContainer box">
            <h2>Login</h2>
            <form action="" method="post" class="form_1" id="loginForm">
                <div class="formGroup alertBox d-none"></div>
                <div class="formGroup">
                    <label for="uname_inp">Username</label>
                    <input type="text" id="uname_inp" name="uname" placeholder="Enter your username" required>
                </div>
                <div class="formGroup">
                    <label for="password_inp">Password</label>
                    <input type="password" id="password_inp" name="password" placeholder="Enter your password" required>
                </div>
                <div class="formGroup captchaBox mb-0">
                    <img src="pages/component/_captcha.php" alt="captcha" class="captcha-img"
                        onclick="this.src='pages/component/_captcha.php?' + new Date().getTime()"
                        title="Click to refresh">
                </div>
                <div class="formGroup">
                    <label for="captcha_inp">Captcha</label>
                    <input type="text" id="captcha_inp" name="captcha" placeholder="Enter above Captcha" required
                        autocomplete="off" maxlength="5" minlength="5">
                </div>
                <div class="btnGroup">
                    <button class="btn submitBtn" type="submit">Sign In</button>
                </div>
            </form>
        </div>
    </main>


    <script src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>

    <script src="assets/js/base.js"></script>

    <script>
    $("#loginForm").submit(function(e) {
        e.preventDefault();

        // disable submit button
        $("form .submitBtn").prop("disabled", true);


        // form validation
        var uname = $("#uname_inp").val();
        var password = $("#password_inp").val();
        var captcha = $("#captcha_inp").val();

        // Basic client-side check.
        if (uname == "" || password == "" || captcha == "") {
            showAlert("All fields are required", "error");
            return false;
        } else if (captcha.length != 5) {
            showAlert("Invalid Captcha", "error");
            return false;
        }

        var formData = $(this).serialize();
        $.ajax({
            type: "POST",
            url: "handlers/loginHandler.php",
            dataType: "json",
            data: formData,
            success: function(data) {
                if (data.status == "success") {
                    showAlert(data.msg, "success");
                    setTimeout(() => {
                        window.location.href = ".?act=1";
                    }, 2000);
                } else {
                    showAlert(data.msg, "error");
                }
            },
            error: function(e) {
                showAlert("Internal error! Try again.", "error");
            }
        })

    })

    function showAlert(msg, type) {
        let cls;
        if (type == "success"){
            $("form .submitBtn").text("Redirecting ...").prop('disabled', true)
            cls = "success";
        } else{
            $("form .submitBtn").prop("disabled", false); // enable submit button
             cls = "error";
        }

        $(".alertBox").text(msg).removeClass("d-none").addClass(cls);
        setTimeout(function() {
            $(".alertBox").addClass("d-none");
        }, 5000);
    }
    </script>

</body>

</html>