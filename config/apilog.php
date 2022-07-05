<?php

return [
    'enabled' => boolval($_SERVER['API_LOGGER'] ?? env('API_LOGGER', false)),
];
