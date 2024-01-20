<?php

function cardFooterStyle($custom_classes = "")
{
    $classes = ['flex items-center p-6 pt-0', $custom_classes];

    return implode(' ', $classes);
}
