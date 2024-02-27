<?php
    $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
    $fields = ["user_name", "user_email", "user_job", "user_mobile", "user_message"];
    $isValid = true;
    $error = '';
    $data = [];

    foreach ($fields as $field) {
        $data[$field] = filter_input(INPUT_POST, $field);
        if (!$data[$field] && $isPost) {
            $isValid = false;
            $error .= "Le champ $field doit être rempli. ";
        }
    }

    if ($isValid && $isPost) {
        $vue = "$sectionPath/contact-sent.php";
    }
