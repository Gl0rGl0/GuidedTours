<?php

namespace App\Services;

class AiContentService
{
    /**
     * Simulates an AI call to enhance a description.
     */
    public function enhance(string $title, string $location): string
    {
        // Simulate network latency
        sleep(1);

        $adjectives = ['breathtaking', 'historic', 'immersive', 'unforgettable', 'vibrant', 'exclusive'];
        $adj = $adjectives[array_rand($adjectives)];

        return "Experience the {$adj} beauty of {$title} at {$location}. " .
               "Our guided tour offers a unique perspective on the rich heritage and hidden gems of this landmark. " .
               "Join our expert guides for a journey through time, perfect for history enthusiasts and casual visitors alike. " .
               "Book now to secure your spot in this high-demand experience!";
    }
}
