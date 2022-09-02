@if(array_key_exists('legal', $data))
    <h3 class="lead collection">
        Legal notes
    </h3>
    <p class="text-info">
        {{ ucfirst($data['legal']['credit_line']) }}
    </p>
@endif
