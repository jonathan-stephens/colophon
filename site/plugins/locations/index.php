<?php

Kirby::plugin('jonathanstephens/locations', [
    'api' => [
        'routes' => [
            [
                'pattern' => 'locations',
                'action' => function () {
                    $locations = [
                        'raleigh-nc' => [
                            'text' => 'Raleigh, NC, US',
                            'value' => 'raleigh-nc',
                            'addressLocality' => 'Raleigh',
                            'addressRegion' => 'North Carolina',
                            'addressCountry' => 'United States',
                            'latitude' => 35.77,
                            'longitude' => -78.63
                        ],
                        'durham-nc' => [
                            'text' => 'Durham, NC, US',
                            'value' => 'durham-nc',
                            'addressLocality' => 'Durham',
                            'addressRegion' => 'North Carolina',
                            'addressCountry' => 'United States',
                            'latitude' => 35.99,
                            'longitude' => -78.89
                        ],
                        'wilmington-nc' => [
                            'text' => 'Wilmington, NC, US',
                            'value' => 'wilmington-nc',
                            'addressLocality' => 'Wilmington',
                            'addressRegion' => 'North Carolina',
                            'addressCountry' => 'United States',
                            'latitude' => 34.07,
                            'longitude' => -77.89
                        ],
                        'premiademar-es' => [
                            'text' => 'Premía de Mar, BCN, ES',
                            'value' => 'premiademar-es',
                            'addressLocality' => 'Premía de Mar',
                            'addressRegion' => 'Barcelona',
                            'addressCountry' => 'Spain',
                            'latitude' => 41.49,
                            'longitude' => 2.36
                        ],
                        'amsterdam-nl' => [
                            'text' => 'Amsterdam, NH, NL',
                            'value' => 'amsterdam-nl',
                            'addressLocality' => 'Amsterdam',
                            'addressRegion' => 'North Holland',
                            'addressCountry' => 'Netherlands',
                            'latitude' => 52.37,
                            'longitude' => 4.89
                        ]
                        // Add more locations as needed
                    ];

                    return $locations;
                }
            ]
        ]
    ],
    'hooks' => [
        'page.update:after' => function ($page, $oldPage, $values) {
            // When a location is selected, save the associated data
            if (isset($values['location']) && $values['location'] !== '') {
                // Get locations data
                $locations = kirby()->api()->call('locations');
                $location = $locations[$values['location']] ?? null;

                if ($location) {
                    // Update page with location details
                    try {
                        $page->update([
                            'addressLocality' => $location['addressLocality'],
                            'addressRegion' => $location['addressRegion'],
                            'addressCountry' => $location['addressCountry'],
                            'latitude' => $location['latitude'],
                            'longitude' => $location['longitude']
                        ]);
                    } catch (Exception $e) {
                        // Handle error
                    }
                }
            }
        }
    ],
    'fields' => [
        // Hide these fields from the Panel as they're handled automatically
        'addressLocality' => [
            'type' => 'hidden'
        ],
        'addressRegion' => [
            'type' => 'hidden'
        ],
        'addressCountry' => [
            'type' => 'hidden'
        ],
        'latitude' => [
            'type' => 'hidden'
        ],
        'longitude' => [
            'type' => 'hidden'
        ]
    ]
]);
