<?php

function cardDescStyle($custom_classes = "")
{
    $classes = ['text-sm text-muted-foreground', $custom_classes];

    return implode(' ', $classes);
}
