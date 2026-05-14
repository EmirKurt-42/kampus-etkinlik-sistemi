<?php
function girisGerekli() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['user_id'])) {
        header('Location: /kampus/pages/giris.php');
        exit;
    }
}

function adminGerekli() {
    girisGerekli();
    if ($_SESSION['user_role'] !== 'admin') {
        header('Location: /kampus/index.php');
        exit;
    }
}