<?php

function cardContentStyle($custom_classes = "")
{
    $classes = ['py-4', $custom_classes];

    return implode(' ', $classes);
}
