<?php

function buttonStyles($custom_classes = "", $variant = 'default', $size = 'default')
{
    $variants = [
        'warning' => 'bg-yellow-400 text-foreground hover:bg-yellow-400/90 focus-visible:ring-yellow-400',
        'sidebar-item' => 'bg-transparent hover:bg-primary border border-muted-foreground/40 text-foreground hover:!text-primary-foreground focus-visible:ring-primary',

        'default' => 'bg-primary text-primary-foreground hover:bg-primary/90 focus-visible:ring-ring',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90 focus-visible:ring-destructive',
        'outline' => 'border border-input bg-transparent hover:bg-accent hover:text-accent-foreground focus-visible:ring-ring',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80 focus-visible:ring-ring',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground focus-visible:ring-ring',
        'link' => 'text-primary underline-offset-4 hover:underline focus-visible:ring-ring',
    ];

    $sizes = [
        'default' => 'h-9 px-4 py-2',
        'sm' => 'h-8 rounded-md px-3 text-xs',
        'lg' => 'h-10 rounded-md px-8',
        'icon' => 'h-9 w-9',
    ];

    $variantClass = $variants[$variant];
    $sizeClass = $sizes[$size];

    $classes = ['inline-flex relative active:scale-95 items-center group justify-center rounded-md text-sm font-medium transition-all duration-200 outline-background focus-visible:outline-2 focus-visible:ring-[5px] disabled:pointer-events-none disabled:opacity-50', $variantClass, $sizeClass, $custom_classes];

    return implode(' ', $classes);
}