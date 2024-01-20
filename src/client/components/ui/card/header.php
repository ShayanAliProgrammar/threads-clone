<?php

function cardHeaderStyle($custom_classes = "")
{
    $classes = ['flex flex-col space-y-1.5 py-4', $custom_classes];

    return implode(' ', $classes);
}
