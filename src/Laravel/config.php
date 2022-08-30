<?php
use Anthonypauwels\AirTable\AirTable;

return [
        'url' => env('AIRTABLE_URL', AirTable::API_URL ),
        'key' => env('AIRTABLE_KEY'),
        'base' => env('AIRTABLE_BASE'),
    ];