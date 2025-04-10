<?php

class ReviewPage extends Page
{
    // Get headshot with fallback
    public function headshot()
    {
        // Be careful not to call the method itself inside the method
        $file = $this->content()->get('headshot')->toFile();
        return $file ?: null;
    }

    // Format the review date
    public function formattedDate()
    {
        if($this->content()->has('reviewDate')) {
            return $this->content()->get('reviewDate')->toDate('F Y');
        }
        return '';
    }

    // Get relationship label - this might be line 14 causing the issue
    public function relationshipLabel()
    {
        $relationships = [
            'mentee' => 'Mentee',
            'mentor' => 'Mentor',
            'direct_report' => 'Direct Report',
            'indirect_report' => 'Indirect Report',
            'colleague' => 'Colleague',
            'client' => 'Client',
            'other' => 'Other'
        ];

        // Get the relationship value safely
        $rel = $this->content()->get('relationship')->value();
        return isset($relationships[$rel]) ? $relationships[$rel] : $rel;
    }

    // Get all tags as an array
    public function tagList()
    {
        if($this->content()->has('tags')) {
            return $this->content()->get('tags')->split();
        }
        return [];
    }

    // Generate structured data for SEO
    public function structuredData()
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $this->content()->get('hed')->value()
            ],
            'reviewBody' => $this->content()->get('review')->value(),
        ];

        if($this->content()->has('reviewDate')) {
            $data['datePublished'] = $this->content()->get('reviewDate')->toDate('Y-m-d');
        }

        if($this->content()->has('role')) {
            $data['author']['jobTitle'] = $this->content()->get('role')->value();
        }

        $headshotFile = $this->content()->get('headshot')->toFile();
        if($headshotFile) {
            $data['author']['image'] = $headshotFile->url();
        }

        return $data;
    }
}
