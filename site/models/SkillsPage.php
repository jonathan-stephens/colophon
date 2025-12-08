<?php

use Kirby\Cms\Page;

class SkillsPage extends Page
{
    /**
     * Returns all unique skill names as available tags
     * Excludes the current skill name being edited to prevent duplicates
     */
    public function skillTags()
    {
        $tags = ['Skill', 'Domain', 'Area', 'Competency', 'Behavior']; // Always include base tags

        // Only try to get skills if the field exists and has content
        try {
            $skillsField = $this->skills();
            if ($skillsField->isNotEmpty()) {
                $skills = $skillsField->toStructure();
                if ($skills && $skills->isNotEmpty()) {
                    $this->collectSkillNames($skills, $tags);
                }
            }
        } catch (Exception $e) {
            // If skills field doesn't exist yet, just return base tags
        }

        // Remove duplicates and return as array of unique values
        return array_values(array_unique($tags));
    }

    /**
     * Recursively collect all skill names from all levels
     */
    protected function collectSkillNames($items, &$tags)
    {
        foreach ($items as $item) {
            // Add the skill name as a tag (preserving spacing and punctuation)
            if ($name = $item->name()->value()) {
                $tags[] = $name;
            }

            // Recursively collect from nested levels
            if ($level2 = $item->level2()->toStructure()) {
                $this->collectSkillNames($level2, $tags);
            }
        }
    }

    /**
     * Hook to auto-add tags before saving
     * This ensures "Skill" and the skill name are always included
     */
    public function writeContent(array $data, string $languageCode = null): bool
    {
        // Process skills structure to add auto-tags
        if (isset($data['skills'])) {
            $data['skills'] = $this->processSkillsForTags($data['skills']);
        }

        return parent::writeContent($data, $languageCode);
    }

    /**
     * Process skills structure to automatically add tags
     */
    protected function processSkillsForTags($skillsYaml)
    {
        // Decode YAML to array
        $skills = yaml_decode($skillsYaml);

        if (is_array($skills)) {
            $skills = $this->addAutoTags($skills);
        }

        // Re-encode to YAML
        return yaml_encode($skills);
    }

    /**
     * Recursively add auto-tags to skills at all levels
     */
    protected function addAutoTags($items, $level = 1)
    {
        // Map levels to semantic hierarchy terms
        $levelTags = [
            1 => 'Domain',      // Broadest category
            2 => 'Area',    // Sub-division
            3 => 'Competency',        // Specific area
            4 => 'Behavior'    // Most granular
        ];

        foreach ($items as &$item) {
            // Get existing tags
            $existingTags = isset($item['tags']) ? $item['tags'] : '';
            $tagsArray = array_filter(array_map('trim', explode(',', $existingTags)));

            // Always add "Skill"
            if (!in_array('Skill', $tagsArray)) {
                $tagsArray[] = 'Skill';
            }

            // Add the hierarchy level tag
            $levelTag = $levelTags[$level];
            if (!in_array($levelTag, $tagsArray)) {
                $tagsArray[] = $levelTag;
            }

            // Add the skill name as a tag
            if (isset($item['name']) && !empty($item['name'])) {
                if (!in_array($item['name'], $tagsArray)) {
                    $tagsArray[] = $item['name'];
                }
            }

            // Save back to item
            $item['tags'] = implode(', ', $tagsArray);

            // Process nested levels
            if (isset($item['level2']) && is_array($item['level2'])) {
                $item['level2'] = $this->addAutoTags($item['level2'], 2);
            }
            if (isset($item['level3']) && is_array($item['level3'])) {
                $item['level3'] = $this->addAutoTags($item['level3'], 3);
            }
            if (isset($item['level4']) && is_array($item['level4'])) {
                $item['level4'] = $this->addAutoTags($item['level4'], 4);
            }
        }

        return $items;
    }

    /**
     * Get all skills across all pages by tag
     * Useful for querying related skills from other page types
     */
    public static function findByTag(string $tag, $pages = null)
    {
        $results = [];
        $pages = $pages ?? site()->index();

        foreach ($pages->filterBy('intendedTemplate', 'skills') as $skillPage) {
            $skills = $skillPage->skills()->toStructure();
            if ($skills->isNotEmpty()) {
                static::searchSkillsByTag($skills, $tag, $results, $skillPage);
            }
        }

        return $results;
    }

    /**
     * Recursively search for skills matching a tag
     */
    protected static function searchSkillsByTag($items, $tag, &$results, $page, $level = 1)
    {
        foreach ($items as $item) {
            $tags = array_map('trim', explode(',', $item->tags()->value()));

            if (in_array($tag, $tags)) {
                $results[] = [
                    'name' => $item->name()->value(),
                    'description' => $item->description()->value(),
                    'tags' => $tags,
                    'level' => $level,
                    'page' => $page
                ];
            }

            // Search nested levels
            if ($level < 4) {
                $nextLevel = 'level' . ($level + 1);
                if ($nested = $item->$nextLevel()->toStructure()) {
                    static::searchSkillsByTag($nested, $tag, $results, $page, $level + 1);
                }
            }
        }
    }
}
