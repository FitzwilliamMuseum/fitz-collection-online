<script
    src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
<script src="/js/slow-viewer.js" type="text/javascript"></script>
<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GOOGLE_ANALYTICS') }}"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());
    gtag('config', '{{ env('APP_GOOGLE_ANALYTICS') }}');
</script>
