<?php

class BookPage extends Page
{
    // Get all quotes for this book
    public function quotes()
    {
        return $this->find('quotes')->children()->listed();
    }

    // Get book author names as a comma-separated string
    public function authorNames()
    {
        $authors = $this->author()->toStructure();
        $names = [];

        foreach($authors as $author) {
            $names[] = $author->name()->value();
        }

        return implode(', ', $names);
    }

    public function coverImage()
    {
        // Simply get the first image file from the page
        return $this->files()->filterBy('template', 'blocks/image')->first();
    }
        // Get book purchase URL if set
    public function purchaseUrl()
    {
        return $this->purchaseLink()->isNotEmpty() ? $this->purchaseLink()->url() : null;
    }

    // Count the number of quotes
    public function quoteCount()
    {
        return $this->quotes()->count();
    }

    // Generate structured data for SEO
    public function structuredData()
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Book',
            'name' => $this->title()->value(),
            'author' => []
        ];

        if ($this->subtitle()->isNotEmpty()) {
            $data['alternativeHeadline'] = $this->subtitle()->value();
        }

        foreach ($this->author()->toStructure() as $author) {
            $data['author'][] = [
                '@type' => 'Person',
                'name' => $author->name()->value(),
                'url' => $author->website()->value()
            ];
        }

        if ($this->coverImage()) {
            $data['image'] = $this->coverImage()->url();
        }

        return $data;
    }
}
