@php $content = '<h1>Hello World</h1>'; @endphp

<html>
<head>
    @stack("head")
</head>
<body>
    @stack('begin')
    {!! $content !!}
    @stack('end')
</body>
</html>