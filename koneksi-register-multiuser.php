<?php

// FUNCTION REGISTER
function registrasi($data)
{
    global $conn;

    $email = strtolower(stripcslashes($data["email_user"]));
    $username = strtolower(stripcslashes($data["username"]));
    $password = mysqli_real_escape_string($conn, $data["password_user"]);
    $password2 = mysqli_real_escape_string($conn, $data["password2_user"]);

    // CEK EMAIL DAN USERNAME SUDAH ADA ATAU BELUM
    $result_email = mysqli_query($conn, "SELECT email FROM tb_user WHERE email = '$email' ");
    $result_username = mysqli_query($conn, "SELECT username FROM tb_user WHERE username = '$username' ");

    // CHECK EMAIL
    if (mysqli_fetch_assoc($result_email)) {
        echo "<script>
		alert('Email yang dibuat sudah ada !');
		</script>";

        return false;
    }

    // CHECK EMAIL
    if (mysqli_fetch_assoc($result_username)) {
        echo "<script>
            alert('Username yang dibuat sudah ada !');
            </script>";

        return false;
    }

    // CEK KONFIRMASI PASSWORD 
    if ($password !== $password2) {
        echo "<script>
		alert('Konfirmasi password salah');
		</script>";

        return false;
    }

    // ENSKRIPSI PASSWORD
    $passwordValid =  password_hash($password2, PASSWORD_DEFAULT);

    // EMAIL MULTI USER
    // Rules :
    // 1. @mhs.unesa.ac.id   => mahasiswa
    // 2. @staff.unesa.ac.id => staff
    // 3. @dosen.unesa.ac.id => dosen

    $check_extension = explode('@', $email);
    $extension_email = strtolower(end($check_extension));
    if ($extension_email == "mhs.unesa.ac.id") {
        $level = "MAHASISWA";
    } else if ($extension_email == "staff.unesa.ac.id") {
        $level = "STAFF";
    } else if ($extension_email == "dosen.unesa.ac.id") {
        $level = "DOSEN";
    }

    // TAMBAHKAN USER BARU KEDATABASE
    $query = "INSERT INTO tb_user(username, email, password, level, verification) 
	VALUES('$username', '$email', '$passwordValid', '$level', 'NO')";

    mysqli_query($conn, $query) or die(mysqli_error($conn));

    return mysqli_affected_rows($conn);
}
