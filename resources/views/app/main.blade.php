<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'miskito.org') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script type="text/javascript" src="https://platform-api.sharethis.com/js/sharethis.js#property=6158029bd602b900198af6ec&product=inline-share-buttons" async="async"></script>
</head>
<body>
  <div id="app"><app></app></div>
  <script src="/js/app.js" defer>
  </script>
</body>
</html>