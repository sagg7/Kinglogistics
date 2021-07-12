<html lang="">
<head>
    <title>Broadcasting test</title>
</head>

<body>

<main>
    <h1>Hi!</h1>

    <p id="online"></p>
</main>

<script>
    window.PUSHER_APP_KEY = '{{ config('broadcasting.connections.pusher.key') }}';
    window.APP_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};
</script>
<script src="{{ asset('js/broadcasting-test.js') }}"></script>

</body>
</html>
