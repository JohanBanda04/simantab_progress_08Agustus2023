<?php

include "../fungsi/koneksi.php";
include "../fungsi/fungsi.php";
include "../classes/class.phpmailer.php";

session_start();
$nama_unit = $_GET['unit'];
$tgl_minta = $_GET['tgl_minta'];
$subbidang_id = $_SESSION['subbidang_id'];

//echo $subbidang_id."::";
$query_get_email_tujuan = mysqli_query($koneksi,"select id_user, username, nama_lengkap ,email from user where 
subbidang_id='$_SESSION[subbidang_id]' and level='kasub_pengguna'");

$data_get_email_tujuan = mysqli_fetch_assoc($query_get_email_tujuan);
$get_email_tujuan = $data_get_email_tujuan['email'];
$get_nama_lengkap_penerima = $data_get_email_tujuan['nama_lengkap'];

echo $get_email_tujuan;



$tgl_sekarang = date('Y-m-d');

$query_cek_kode_brg = mysqli_query($koneksi,"select * from sementara where unit='$_SESSION[username]'
and user_id='$_SESSION[user_id]' and tgl_permintaan='$tgl_sekarang'");

$array_cek_4 = array();
while ($dts = mysqli_fetch_array($query_cek_kode_brg)){
    array_push($array_cek_4,$dts['jumlah']);
}

$array_cek = array();
foreach ($array_cek_4 as $val){
    if($val > 4){
        array_push($array_cek,"tidak memenuhi syarat");
    } else if ($val <=4 ){
        array_push($array_cek,"memenuhi syarat");
    }
}

if(in_array("tidak memenuhi syarat",$array_cek)){
    echo '<script language="javascript">alert("Jumlah Kuantitas per Item ada yang melebihi 4 (satuan)");
document.location="index.php?p=formpesan";</script>';
} else {

    $query_cek_eksis_v2 = mysqli_query($koneksi,"select count(id_sementara) as jumlah from 
sementara where unit='$_SESSION[username]'
and user_id='$_SESSION[user_id]' and tgl_permintaan='$tgl_sekarang'");
    $jumlah = mysqli_fetch_assoc($query_cek_eksis_v2);

    if($jumlah['jumlah']==0){
        echo '<script language="javascript">alert("Tidak ada permintaan data");
document.location="index.php?p=formpesan";</script>';
    } else {
        $queryJenis = mysqli_query($koneksi, "UPDATE sementara set status_acc='Pengajuan Kasub' where
id_subbidang=$_SESSION[subbidang_id] and user_id=$_SESSION[user_id] and status_acc='Permintaan Baru'");


        //metode kirim email
        $mail = new PHPMailer;
        $mail->IsSMTP();
        $mail->SMTPSecure = "ssl";
        $mail->Host = "smtp.gmail.com"; //host email
//$mail->SMTPDebug = 2;
        $mail->Port = 465;
        $mail->SMTPAuth = true;
        $mail->Username = "senderforemail340@gmail.com"; //user email
        $mail->Password = "mtklbsimtazfowfi"; //password email
        $mail->SetFrom("senderforemail340@gmail.com", "Permintaan Baru"); //set email pengirim
        $mail->Subject = "Permintaan Baru"; //subyek email
        $mail->AddAddress($get_email_tujuan);  // email tujuan
        $mail->MsgHTML("Halo, " . $get_nama_lengkap_penerima . "
    Terdapat Permintaan Barang dari " . $_SESSION['nama_lengkap'] .
            ", pada tanggal " . tanggal_indo($tgl_sekarang) . ",  segera cek aplikasi SIMANTAB melalui akun Anda"); //pesan

        if ($queryJenis && $mail->Send()) {
            echo '<script language="javascript">alert("Pemberitahuan Kasub berhasil !!!");
document.location="index.php?p=formpesan_table";</script>';
        } else {
            echo 'error' . mysqli_error($koneksi);
        }
    }
//    $queryJenis = mysqli_query($koneksi, "UPDATE sementara set status_acc='Pengajuan Kasub' where
//id_subbidang=$_SESSION[subbidang_id] and user_id=$_SESSION[user_id] and status_acc='Permintaan Baru'");
//
//
//    //metode kirim email
//    $mail = new PHPMailer;
//    $mail->IsSMTP();
//    $mail->SMTPSecure = "ssl";
//    $mail->Host = "smtp.gmail.com"; //host email
////$mail->SMTPDebug = 2;
//    $mail->Port = 465;
//    $mail->SMTPAuth = true;
//    $mail->Username = "senderforemail340@gmail.com"; //user email
//    $mail->Password = "mtklbsimtazfowfi"; //password email
//    $mail->SetFrom("senderforemail340@gmail.com","Permintaan Baru"); //set email pengirim
//    $mail->Subject = "Permintaan Baru"; //subyek email
//    $mail->AddAddress($get_email_tujuan);  // email tujuan
//    $mail->MsgHTML("Halo, ".$get_nama_lengkap_penerima."
//    Terdapat Permintaan Barang dari ".$_SESSION['nama_lengkap'].
//        ", pada tanggal ".tanggal_indo($tgl_sekarang).",  segera cek aplikasi SIMANTAB melalui akun Anda"); //pesan
//
//    if ($queryJenis && $mail->Send()) {
//        echo '<script language="javascript">alert("Pemberitahuan Kasub berhasil !!!");
//document.location="index.php?p=formpesan_table";</script>';
//    } else {
//        echo 'error' . mysqli_error($koneksi);
//    }
}

//-----------------------------------------------------

//ini query nya dia ngeblast langsung
//$queryJenis = mysqli_query($koneksi, "UPDATE sementara set status_acc='Pengajuan Kasub' where
//id_subbidang=$_SESSION[subbidang_id] and user_id=$_SESSION[user_id] and status_acc='Permintaan Baru'");
//
//
//            if ($queryJenis) {
//                echo '<script language="javascript">alert("Pemberitahuan Kasub berhasil !!!"); document.location="index.php?p=formpesan";</script>';
//            } else {
//                echo 'error' . mysqli_error($koneksi);
//            }






?>
