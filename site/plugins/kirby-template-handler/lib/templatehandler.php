<?php

class TemplateHandler {
    protected $page;
    protected $kirby;
    protected $site;

    public function __construct($page = null) {
        $this->kirby = kirby();
        $this->site = site();
        $this->page = $page ?? page();
    }

    /**
     * Determine template based on various conditions
     */
    public function getTemplate($conditions = []) {
        // Early return if page has forced template
        if ($this->page->template_override()->isNotEmpty()) {
            return $this->page->template_override()->toString();
        }

        // Check URL patterns
        if ($template = $this->matchUrlPattern()) {
            return $template;
        }

        // Check date-based conditions
        if ($template = $this->matchDateCondition()) {
            return $template;
        }

        // Check role-based conditions
        if ($template = $this->matchUserRole()) {
            return $template;
        }

        // Check language-based conditions
        if ($template = $this->matchLanguage()) {
            return $template;
        }

        return option('jonathanstephens.template-handler.defaultTemplate', 'default');
    }

    /**
     * Match URL patterns including wildcards and parameters
     */
    protected function matchUrlPattern() {
        $patterns = [
            // Exact match
            'about' => 'about-template',
            // Wildcard match
            'projects/*' => 'project-detail',
            // Parameter match
            'category/(:any)' => 'category-template',
            // Multiple parameters
            'archive/(:num)/(:any)' => 'archive-detail',
            // Regular expression match
            // '~^journal/\d{4}/\d{2}$~' => 'journal-archive'
        ];

        $currentPath = $this->page->uri();

        foreach ($patterns as $pattern => $template) {
            // Handle regex patterns
            if (substr($pattern, 0, 1) === '~') {
                if (preg_match($pattern, $currentPath)) {
                    return $template;
                }
                continue;
            }

            // Convert Kirby-style patterns to regex
            $pattern = str_replace(
                ['(:any)', '(:num)', '(:all)', '*'],
                ['([^\/]+)', '(\d+)', '(.*)', '([^\/]+)'],
                $pattern
            );

            if (preg_match('#^' . $pattern . '$#', $currentPath)) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Match date-based conditions
     */
    protected function matchDateCondition() {
        $dateRules = [
            // Weekend template
            'isWeekend' => function() {
                return (date('N') >= 6) ? 'weekend-template' : null;
            },
            // Holiday template
            'isHoliday' => function() {
                $holidays = ['12-25', '01-01', '07-04'];
                return (in_array(date('m-d'), $holidays)) ? 'holiday-template' : null;
            },
            // Time-based template
            'timeOfDay' => function() {
                $hour = (int)date('H');
                if ($hour >= 22 || $hour < 6) return 'night-template';
                if ($hour >= 6 && $hour < 12) return 'morning-template';
                if ($hour >= 12 && $hour < 17) return 'afternoon-template';
                return 'evening-template';
            }
        ];

        foreach ($dateRules as $rule => $callback) {
            if ($template = $callback()) {
                return $template;
            }
        }

        return null;
    }

    /**
     * Match user role conditions
     */
    protected function matchUserRole() {
        if ($user = $this->kirby->user()) {
            $roleTemplates = [
                'admin' => 'admin-template',
                'editor' => 'editor-template',
                'contributor' => 'contributor-template'
            ];

            return $roleTemplates[$user->role()->id()] ?? null;
        }

        return null;
    }

    /**
     * Match language conditions
     */
    protected function matchLanguage() {
        if ($this->kirby->multilang()) {
            $languageTemplates = [
                'en' => 'english-template',
                'de' => 'german-template',
                'fr' => 'french-template'
            ];

            return $languageTemplates[$this->kirby->language()->code()] ?? null;
        }

        return null;
    }
}
