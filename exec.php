<?php

    // Этот файл надо выполнить один раз
    // Оне регает нашего бота в системе DTF

    $url = 'https://api.dtf.ru/v1.9/webhooks/add';
    $post = array(
      'event' => 'new_comment',
      'url'   => 'https://dtf.radkopeter.ru/comment.php' // <- скрипт, которому DTF будет раз в секунду сообщать о новых комментах
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Accept: application/json',
      'Content-Type: multipart/form-data',
      'X-Device-Token: ' . 'ТОКЕН БОТА'
    ));
    curl_exec($ch);
    curl_close($ch);
    return true;