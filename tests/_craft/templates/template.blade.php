@php
$content = $content ?? '';
@endphp
<html>
    {!! Func::head() !!}
<head>

</head>
<body>
    {!! Func::beginBody() !!}
    {!! $content !!}
    {!! Func::endBody() !!}
</body>
</html>