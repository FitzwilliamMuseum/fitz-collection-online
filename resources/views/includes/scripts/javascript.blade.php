<script src="{{ mix('js/app.js') }}"></script>

@hasSection('audio-guide')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.10/plyr.min.js"></script>
<script>
// Change "{}" to your options:
// https://github.com/sampotts/plyr/#options
var controls =
[
    'restart', // Restart playback
    'play', // Play/pause playback
    'progress', // The progress bar and scrubber for playback and buffering
    'current-time', // The current time of playback
    'duration', // The full duration of the media
    'mute', // Toggle mute
    'volume', // Volume control
    'settings', // Settings menu
    'download', // Show a download button with a link to either the current source or a custom URL you specify in your options
];
const player = new Plyr('.player', { controls });

// Expose player so it can be used from the console
window.player = player;
</script>
@endif

@hasSection('height-test')
  @yield('height-test')
@endif
<script>
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
</script>


<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('APP_GOOGLE_ANALYTICS') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('config', '{{ env('APP_GOOGLE_ANALYTICS') }}', { cookie_flags: 'SameSite=None;Secure' });
</script>
