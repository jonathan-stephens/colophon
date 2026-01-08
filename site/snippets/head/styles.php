  <!-- Styles -->
    <link rel="preload" href="<?= Bnomei\Fingerprint::url('/assets/fonts/ibm-plex-sans/variable-roman.woff2');?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= Bnomei\Fingerprint::url('/assets/fonts/ibm-plex-sans/variable-italic.woff2');?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= Bnomei\Fingerprint::url('/assets/fonts/ibm-plex-serif/serif-medium.woff2'); ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= Bnomei\Fingerprint::url('/assets/fonts/ibm-plex-serif/serif-bold.woff2'); ?>" as="font" type="font/woff2" crossorigin>

    <?= Bnomei\Fingerprint::css('/assets/css/main.css'); ?>
    <?= Bnomei\Fingerprint::css('/media/plugins/mauricerenck/komments/komments.css', ['defer' => true]) ?>
