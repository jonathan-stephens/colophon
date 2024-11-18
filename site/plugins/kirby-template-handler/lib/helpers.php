<?php

/**
 * Helper function to use in templates
 */
function useTemplate($conditions = []) {
    $handler = new TemplateHandler();
    return $handler->getTemplate($conditions);
}
