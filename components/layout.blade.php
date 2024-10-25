<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ $title }}</title>
    @if (isset($description))
        <meta name="description" content="{{ $description }}">
    @endif
    @if (BladeFactory::getInstance()->getLocale() === 'fa')
        <link rel="stylesheet" href="{{ asset('css/styles-rtl.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @endif

    {{ $styles ?? '' }}
</head>

<body>
    @include('components::header')
    @include('components::breadcrumb')

    <div class="content">
        {{ $slot }}
    </div>

    @include('components::footer')


    {{ $scripts ?? '' }}
</body>

</html>
