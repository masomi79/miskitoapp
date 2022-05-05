<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="el diccionario en linea miskitu - español">
  <meta name="keywords" content="miskito, miskitu, misquito"><meta property="og:type" content="ページの種類" />
  <meta property="og:title" content="miskito.org" />
  <meta property="og:description" content="el diccionario en linea miskitu - español" />
  <meta property="og:site_name" content="miskito.org" />
  <meta property="og:url" content="https://miskito.org" />
  <meta property="og:image" content="https://miskito.org/img/logo1.png" />
  <title>{{ config('app.name', 'miskito.org') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://kit.fontawesome.com/18661ea3b2.js" crossorigin="anonymous"></script></head>
<body>
  <div id="app"><app></app></div>
  <script src="/js/app.js" defer>
  </script>
</body>
</html>