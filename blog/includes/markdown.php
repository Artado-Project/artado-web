<?php
/**
 * Simple Markdown to HTML Parser
 */
function markdown_to_html($markdown) {
    $markdown = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $markdown);
    $markdown = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $markdown);
    $markdown = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $markdown);
    
    $markdown = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $markdown);
    
    $markdown = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $markdown);
    
    $markdown = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" class="text-blue-500 underline">$1</a>', $markdown);
    
    $markdown = nl2br($markdown);
    
    return $markdown;
}
