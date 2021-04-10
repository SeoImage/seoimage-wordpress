<?php

if (!defined('ABSPATH')) {
    exit;
}

$tab = isset($_GET['tab']) ? $_GET['tab'] : null;

switch ($tab) {
   case 'support':
        include_once __DIR__ . '/_support.php';

        return;
    break;
}

$totalAltNoOptimize = imageseo_get_service('QueryImages')->getNumberImageNonOptimizeAlt();
$percentLoose = imageseo_get_service('ImageLibrary')->getPercentLooseTraffic($totalAltNoOptimize);
$classesBlock = ['fl-1'];
if ($percentLoose >= 2) {
    $classesBlock[] = 'imageseo-mr-3';
}

?>

<div id="wrap-imageseo">
    <div class="wrap">
		<h1 class="imageseo__page-title"><?php _e('Image SEO Settings', 'imageseo'); ?></h1>
		<?php if (!imageseo_allowed()) { ?>
            <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/banner-welcome.php'; ?>
        <?php } ?>
		<div class="imageseo-flex imageseo-mt-3">
            <div class="<?php echo implode(' ', $classesBlock); ?>">
                <div class="imageseo-block">
                    <div class="imageseo-block__inner">
                        <div class="imageseo-flex">
                            <div class="imageseo-mr-2">
                                <div class="imageseo-icons imageseo-icons--oval">
                                    <img src="<?php echo IMAGESEO_URL_DIST . '/images/search.svg'; ?>" alt="">
                                </div>
                            </div>
                            <div class="fl-1">
                                <p class="imageseo-mb-0"><strong><?php _e('SEO Fact : More than 20% of Google Organic Traffic comes from image searches', 'imageseo'); ?></strong></p>
                                <p><?php _e('Start optimizing your image alt texts and names and grow your traffic!', 'imageseo'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($percentLoose >= 2) { ?>
                <div class="fl-1">
                    <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/optimization.php'; ?>
                </div>
            <?php } ?>
        </div>
        <div class="imageseo-flex imageseo-mt-3 imageseo-mb-3">
            <div class="fl-2 imageseo-mr-3">
                <?php if (imageseo_allowed()) { ?>

                    <div class="imageseo-block">
                        <div class="imageseo-block__inner imageseo-block__inner--head">
                            <h3><?php _e('Your API Key', 'imageseo'); ?></h3>
                        </div>
                        <div class="imageseo-block__inner">
                            <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/api-key.php'; ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/register.php'; ?>
                <?php } ?>
            </div>
            <div class="fl-3">
                <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/overview.php'; ?>
            </div>
        </div>

        <form method="post" id="mainform" action="<?php echo esc_url(admin_url('options.php')); ?>">
            <div class="imageseo-mb-3">
                <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/settings.php'; ?>
            </div>
            <div >
                <?php include_once IMAGESEO_TEMPLATES_ADMIN_PAGES . '/elements/social-media.php'; ?>
            </div>
            <?php

            settings_fields(IMAGESEO_OPTION_GROUP);
            submit_button();
            ?>
        </form>
        <hr>
    </div>
</div>
