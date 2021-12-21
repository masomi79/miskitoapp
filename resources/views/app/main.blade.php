<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ config('app.name', 'miskito.org') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/18661ea3b2.js" crossorigin="anonymous"></script></head>
<body>
  <div id="app"><app></app></div>
  <script src="/js/app.js" defer>
  </script>
</body>
</html>