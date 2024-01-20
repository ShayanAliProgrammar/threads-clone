<?php

function cardStyle($custom_classes = "")
{
    $classes = ['rounded-xl h-max border pb-2 px-6 bg-card text-card-foreground shadow', $custom_classes];

    return implode(' ', $classes);
}
