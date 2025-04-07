<?php

class QuotePage extends Page
{
    // Get parent book
    public function book()
    {
        // The quotes are inside a quotes container folder
        // so we need to go up two levels to get the book
        return $this->parent()->parent();
    }

    // Get book title
    public function bookTitle()
    {
        return $this->book()->title()->value();
    }

    // Get formatted quote with proper quotation marks
    public function formattedQuote()
    {
        return '"' . $this->quoteText()->value() . '"';
    }

    // Get citation with page number
    public function citation()
    {
        $citation = $this->bookTitle();

        if ($this->pageNumber()->isNotEmpty()) {
            $citation .= ', p. ' . $this->pageNumber()->value();
        }

        return $citation;
    }

    // Get next quote in the book
    public function nextQuote()
    {
        return $this->nextListed();
    }

    // Get previous quote in the book
    public function prevQuote()
    {
        return $this->prevListed();
    }

    // Generate a shareable text version of the quote
    public function shareText()
    {
        $text = $this->formattedQuote();
        $text .= ' — ' . $this->book()->authorNames();
        $text .= ', ' . $this->bookTitle();

        if ($this->pageNumber()->isNotEmpty()) {
            $text .= ', p. ' . $this->pageNumber()->value();
        }

        return $text;
    }
}
