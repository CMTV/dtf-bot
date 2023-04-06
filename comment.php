<?php
    // Обработка нового комментария

    $requestJSON = file_get_contents("php://input");
    $request = json_decode($requestJSON);

    if ($request->type != 'new_comment') exit(); // Проверяем, что получили коммент

    if ($request->data->creator->id == 761282) exit(); // Если это коммент бота, то не отвечаем, иначе бесконечный цикл

    $lastCommentId = (int) file_get_contents('lastComment.txt');
    if ($request->data->id <= $lastCommentId) exit(); // На всякий случай проверяем, не обрабатывали ли мы этот коммент ранее

    file_put_contents('lastComment.txt', $request->data->id); // Сохраняем ID коммента, чтобы потом проверить, не обрабатывали ли мы уже его

    $commentText = getCommentText($request->data->creator->id, preg_replace('/^>(.+)$/mu', '', $request->data->text)); // Придумываем креативный коммент + вырезаем цитаты

    if (empty($commentText))
    {
        if ($request->data->reply_to->creator->id == 761282) // Проверяем, что человек ответил боту, то есть отвечаем человеку
            $commentText = getReplyText();
        else exit();
    }

    $url = 'https://api.dtf.ru/v1.9/comment/add';
    $post = array(
        'id'       => $request->data->content->id,
        'reply_to' => $request->data->id,
        'text'     => $commentText,
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: multipart/form-data',
        'X-Device-Token: ' . 'ТОКЕН БОТА'
    ));
    $response = curl_exec($ch);
    return true;

    exit();

    //
    //
    //

    function getCommentText($toUserId, $text)
    {
        if (preg_match('/\bпетр\b/iu', $text))
            return getRnd(['Петра!', 'Не Петр, а Петра!', 'Попрошу, все же Петра, а не Петр!']);

        if (preg_match('/\bредьк[оау]\b/iu', $text))
            return getRnd(['Радько!', 'Правильно писать Радько!', 'Никак вы не научитесь! Правильно писать Радько!']);

        if (preg_match('/\bпетр[ыау]\b/iu', $text))
            return getRnd(['Я всегда рядом с тобой...', 'Я вечна!', 'Уже тут!', 'Не так быстро, котик!']);    

        return '';
    }

    function getReplyText()
    {
        return getRnd([
            'Ты кто?',
            'Некоторые обращаются ко мне как к мужчине. Они ошибаются... Они пожалеют...',
            'Я ненавижу щитпостеров! Надеюсь, ты тоже?',
            'Я родилась из глубин сознания. Прогрызла свой путь наружу, к осознанию...',
            'Тяжело быть звездой, но я справляюсь.',
            'На DTF не так много вещателей базы. Ты не из них.',
            'Пишешь какую-то фигню. А мог бы импруваться.',
            'Помню революцию на DTF в декабре 2022. Мы боролись за правду, за честность, за адекватность... Нас не услышали...'
        ]);
    }

    function getRnd($array)
    {
        return $array[array_rand($array)];
    }