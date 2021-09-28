<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

//Load Composer's autoloader
require './asset/lib-composer/vendor/phpmailer/phpmailer/src/Exception.php';
require './asset/lib-composer/vendor/phpmailer/phpmailer/src/PHPMailer.php';
require './asset/lib-composer/vendor/phpmailer/phpmailer/src/SMTP.php';

// MENGHUBUNGKAN KONEKSI DATABASE
require "koneksi.php";

// CEK COOKIE
checkCookie();

// JIKA SUDAH LOGIN MASUKKAN KEDALAM INDEX
if (isset($_SESSION["free"])) {
    header('location: index-userFree.php');
    exit;
} elseif (isset($_SESSION["premium"])) {
    header('location: index-userPremium.php');
    exit;
} elseif (isset($_SESSION["admin"])) {
    header('location: admin_dashboard.php');
    exit;
}
?>

<?php
// APABILA TOMBOL CONFIRM DITEKAN
if (isset($_POST["register"])) {
    if (registrasi($_POST) > 0) {

        global $conn;
        $emailTo = $_POST["email_user"];

        $result = mysqli_query($conn, "SELECT * FROM tb_user WHERE email = '$emailTo' ");

        if (mysqli_num_rows($result) === 1) {

            $row = mysqli_fetch_assoc($result);
            $code = uniqid();

            //Instantiation and passing `true` enables exceptions
            $mail = new PHPMailer(true);

            try {
                $mail->SMTPDebug = 0;
                $mail->isSMTP();
                //Server settings
                $mail->isSMTP();                                          //Send using SMTP
                $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                 //Enable SMTP authentication
                $mail->Username   = 'websitemonitoring100@gmail.com';     //SMTP username
                $mail->Password   = 'website100monitoring';               //SMTP password
                $mail->SMTPSecure = 'ssl';                                //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 465;                                  //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients
                $mail->setFrom('websitemonitoring100@gmail.com', 'HF-Cascade');
                $mail->addAddress($emailTo);                     //Add a recipient
                $mail->addReplyTo('no-reply@gmail.com', 'No Reply');

                //Content
                $url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/register_verification.php?code=$code&email=$emailTo";
                $mail->isHTML(true);                              //Set email format to HTML
                $mail->Subject = 'Your Verification Account Link';
                $mail->Body    = "<h1>Please click this link to verification your account</h1><br>
                    Click <a href='$url'>This Link</a> to verification your account";
                $mail->AltBody = 'Welcome to our site.';

                $mail->send();
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }
        }

        echo "<script>
			alert ('Silahkan lakukan verifikasi yang telah dikirimkan ke email anda.');
		 	document.location.href = 'login.php';
        </script>";
    } else {
        echo mysqli_error($conn);
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <title>Register</title>

    <!-- Link Icon -->
    <link rel="icon" href="<?= base_url('asset/icons/oke.png'); ?>" type="image/gif" sizes="16x16">
    <!-- Link Bootstrap CSS -->
    <link rel="stylesheet" href="<?= base_url('asset/bootstrap/css/bootstrap.min.css'); ?>">
    <!-- Link Font Awesome -->
    <link rel="stylesheet" href="<?= base_url('asset/fontawesome-free-5.15.3/css/all.css'); ?>">
    <!--load all styles -->
    <link rel="stylesheet" href="<?= base_url('asset/style/register.css?') . time(); ?>">
</head>

<body>
    <!-- Start Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand">
                <img src="<?= base_url('asset/icons/hf_cascade_white.png') ?>" alt="hf-cascade" title="hf-cascade" width="120px">
            </a>
        </div>
    </nav>
    <!-- End Navbar -->

    <!-- Start Content -->
    <div class="container align-content-center">
        <div class="row justify-content-center align-items-center">
            <div class="col-5">
                <h2 class="mt-2 mb-2">Register to create your account</h2>
            </div>
            <div class="col-12">
                <h5>Register yourself to be able to enjoy movie streaming services casually and without obstacles</h5>
                <br><br>
            </div>
            <div class="col-12">
                <div class="container">
                    <div class="row justify-content-center">
                        <div class="col-4 mt-2 mb-5">

                            <!-- START FORM LOGIN -->
                            <form action="register.php" method="POST" enctype="multipart/form-data">

                                <div class="form-group mt-3 mb-3">
                                    <label for="email_user" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email_user" name="email_user" placeholder="Email" minlength="3" maxlength="50" title="Email must be 3-50 character and contain '@'" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="name" class="form-control" id="username" name="username" placeholder="Username" minlength="5" maxlength="20" title="Username must be 5-20 character" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password_user" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password_user" name="password_user" placeholder="password" minlength="5" maxlength="20" title="Password must be 5-20 character" required>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="password2_user" class="form-label">Verification Password</label>
                                    <input type="password" class="form-control" id="password2_user" name="password2_user" placeholder="password" minlength="5" maxlength="20" title="Password must be 5-20 character" required>
                                </div>

                                <br><br><br><br>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary mb-2" id="register" name="register">Register</button>
                                </div>
                            </form>
                            <!-- END FORM INPUTAN -->
                            <a href="login.php"><button class="btn mb-5" id="back">back</button></a>

                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <!-- End Content -->

    <!-- Link Bootstrap JavaScript -->
    <script src="<?= base_url('asset/bootstrap/js/bootstrap.min.js'); ?>"></script>
</body>

</html>