<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="utf-8">
<title>Новый материал</title>
</head>
<body style="font-family:Arial,sans-serif;color:#171717;max-width:620px;margin:40px auto;padding:24px">
<p style="color:#ff4f00;font-weight:bold;font-size:28px">почин.</p>
<h1 style="font-size:28px">Создан новый материал</h1>
<h2>{{ $article->title }}</h2>
<p>{{ $article->excerpt }}</p>
<p>Тема: {{ $article->category }}<br> Состояние: {{ $article->status === 'published' ? 'Опубликовано' : 'Черновик' }}</p>
<p>
<a style="display:inline-block;background:#ff4f00;color:#fff;padding:14px 22px;text-decoration:none" href="{{ route('articles.show',$article) }}">Открыть материал</a>
</p>
<p style="color:#666;font-size:13px">Это уведомление для модератора сервиса «Почин».</p>
</body>
</html>
