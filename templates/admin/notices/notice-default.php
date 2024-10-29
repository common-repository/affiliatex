<div class="notice is-dismissible notice-affiliatex-plugin" data-notice="<?php echo esc_attr($this->get_name()) ?>">
    <div class="affx-notice">
        <span class="affx-notice__icon">
            <img src="<?php echo esc_url(AFFILIATEX_PLUGIN_URL . 'src/images/logo.svg') ?>" alt="<?php _e('AffiliateX Logo', 'affiliatex') ?>" />
        </span>
        <div class="affx-notice__content">
            <h2><?php echo esc_html($this->get_title()) ?></h2>
            <div class="affx-notice__content_description">
                <?php echo wp_kses_post($this->get_description()) ?>
            </div>
            <div class="affx-notice__options">
               <?php echo wp_kses_post($this->render_option_buttons()) ?>
            </div>
        </div>
    </div>
</div>
