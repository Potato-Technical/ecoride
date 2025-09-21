<?php
// tools/test_password.php
$hash = '$2y$10$mC6LRo4Sg2VBYUhyy/9byeApjGgPdH3h8KRTTs6eCuH94BHLG6LN2';
$plain = 'Admin123!';

if (password_verify($plain, $hash)) {
    echo "OK — password matches\n";
} else {
    echo "KO — password doesn't match\n";
}
