<?php

function cardTitleStyle($custom_classes = "")
{
    $classes = ['font-semibold leading-none tracking-tight', $custom_classes];

    return implode(' ', $classes);
}
