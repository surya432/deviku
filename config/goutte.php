<?php

return [

    /*
     |-----------------------------------------------------------------------
     | Guzzle Client Configuration
     |-----------------------------------------------------------------------
     |
     | A collection of default request options to apply to each request
     | dispatched by the GuzzleHttp client.
     |
     | @see {@link http://docs.guzzlephp.org/en/stable/request-options.html}
     */

    'client' => [
        'verify' => false,
        'timeout' => 300,
        // 'allow_redirects' => false,
        // 'cookies' => true,
    ],
];
