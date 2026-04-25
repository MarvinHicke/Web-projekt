<?php
/**
 * Temporary Sprint 1 data.
 * Later this should come from the database.(by A)
 */

$artworks = [
    [
        'id' => 1010,
        'title' => 'Mona Lisa',
        'artist_id' => 1,
        'artist_first_name' => 'Leonardo',
        'artist_last_name' => 'da Vinci',
        'year' => 1503,
        'image' => 'images/works/medium/001010.jpg',
        'large_image' => 'images/works/large/001010.jpg',
        'genre' => 'Portrait',
        'subjects' => ['Woman', 'Smile'],
        'average_rating' => 4.8,
        'review_count' => 18,
        'gallery' => 'Louvre Museum',
    ],
    [
        'id' => 1020,
        'title' => 'Girl with a Pearl Earring',
        'artist_id' => 2,
        'artist_first_name' => 'Johannes',
        'artist_last_name' => 'Vermeer',
        'year' => 1665,
        'image' => 'images/works/medium/001020.jpg',
        'large_image' => 'images/works/large/001020.jpg',
        'genre' => 'Portrait',
        'subjects' => ['Girl', 'Pearl'],
        'average_rating' => 4.6,
        'review_count' => 13,
        'gallery' => 'Mauritshuis',
    ],
    [
        'id' => 1030,
        'title' => 'The Starry Night',
        'artist_id' => 3,
        'artist_first_name' => 'Vincent',
        'artist_last_name' => 'van Gogh',
        'year' => 1889,
        'image' => 'images/works/medium/001030.jpg',
        'large_image' => 'images/works/large/001030.jpg',
        'genre' => 'Landscape',
        'subjects' => ['Night', 'Village', 'Sky'],
        'average_rating' => 4.9,
        'review_count' => 25,
        'gallery' => 'Museum of Modern Art',
    ],
    [
        'id' => 1040,
        'title' => 'The Kiss',
        'artist_id' => 4,
        'artist_first_name' => 'Gustav',
        'artist_last_name' => 'Klimt',
        'year' => 1908,
        'image' => 'images/works/medium/001040.jpg',
        'large_image' => 'images/works/large/001040.jpg',
        'genre' => 'Symbolism',
        'subjects' => ['Love', 'Gold'],
        'average_rating' => 4.7,
        'review_count' => 15,
        'gallery' => 'Belvedere',
    ],
];

$artists = [
    [
        'id' => 1,
        'first_name' => 'Leonardo',
        'last_name' => 'da Vinci',
        'review_count' => 31,
        'image' => 'images/artists/medium/1.jpg',
    ],
    [
        'id' => 2,
        'first_name' => 'Johannes',
        'last_name' => 'Vermeer',
        'review_count' => 19,
        'image' => 'images/artists/medium/2.jpg',
    ],
    [
        'id' => 3,
        'first_name' => 'Vincent',
        'last_name' => 'van Gogh',
        'review_count' => 44,
        'image' => 'images/artists/medium/10.jpg',
    ],
    [
        'id' => 4,
        'first_name' => 'Gustav',
        'last_name' => 'Klimt',
        'review_count' => 27,
        'image' => 'images/artists/medium/12.jpg',
    ],
];

$reviews = [
    [
        'artwork_id' => 1030,
        'artwork_title' => 'The Starry Night',
        'user' => 'Mia',
        'rating' => 5,
        'text' => 'Beautiful colours and movement.',
        'date' => '2026-04-21',
    ],
    [
        'artwork_id' => 1010,
        'artwork_title' => 'Mona Lisa',
        'user' => 'Noah',
        'rating' => 4,
        'text' => 'Very interesting face expression.',
        'date' => '2026-04-20',
    ],
    [
        'artwork_id' => 1020,
        'artwork_title' => 'Girl with a Pearl Earring',
        'user' => 'Emma',
        'rating' => 5,
        'text' => 'Simple but very strong.',
        'date' => '2026-04-18',
    ],
];
