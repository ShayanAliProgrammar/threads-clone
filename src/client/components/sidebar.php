<aside class="flex py-3 flex-col !h-[100%] gap-3 max-w-72 transition-all duration-300 w-full p-1 xs:!px-2" id="sidebar" on:visible='/js/sidebar.js'>
    <a href="/" class="<?= buttonStyles('sc-415px:!px-4 sc-415px:py-2 !p-1.5 !w-full sc-415px:!justify-start', 'sidebar-item') ?>" role="button">
        <?php homeIcon('w-5 h-5'); ?>
        <span class="!ml-2 sr-only sc-415px:not-sr-only">Home</span>
    </a>
    <a href="/search" class="<?= buttonStyles('sc-415px:!px-4 sc-415px:py-2 !p-1.5 !w-full sc-415px:!justify-start', 'sidebar-item') ?>" role="button">
        <?php searchIcon('w-5 h-5'); ?>
        <span class="!ml-2 sr-only sc-415px:not-sr-only">Search</span>
    </a>
    <a href="/auth/profile" class="<?= buttonStyles('sc-415px:!px-4 sc-415px:py-2 !p-1.5 !w-full sc-415px:!justify-start', 'sidebar-item') ?>" role="button">
        <?php profileIcon('w-5 h-5'); ?>
        <span class="!ml-2 sr-only sc-415px:not-sr-only">Profile</span>
    </a>
    <div class="mt-auto">
        <a href="/api/auth/logout" class="<?= buttonStyles('sc-415px:!px-4 sc-415px:py-2 !p-1.5 !w-full sc-415px:!justify-start !p-0') ?>" data-no-swup role="button">
            <?php logoutIcon('w-5 h-5'); ?>
            <span class="!ml-2 sr-only sc-415px:not-sr-only">Logout</span>
        </a>
    </div>
</aside>