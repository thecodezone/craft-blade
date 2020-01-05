@php
$content = $content ?? '';
@endphp
<html>
    {!! Fn::head() !!}
<head>

</head>
<body>
    {!! Fn::beginBody() !!}
    {!! $content !!}
    {!! Fn::endBody() !!}
</body>
</html>