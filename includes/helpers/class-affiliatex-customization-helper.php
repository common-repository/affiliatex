<?php
/**
 * AFFILIATE Customization Helper.
 *
 * @package AFFILIATE
 */

namespace AffiliateX\Blocks;

if ( ! class_exists( 'AffiliateX_Customization_Helper' ) ) {

    /**
     * Class AffiliateX_Customization_Helper.
     */
    class AffiliateX_Customization_Helper {

        private static $customization_data;
        private static $plugin_url;

        public static function init() {
            self::$customization_data = get_option('affiliatex_customization_data', []);
            self::$plugin_url = plugin_dir_url(AFFILIATEX_PLUGIN_FILE);
        }

        public static function apply_customizations($attributes) {
            foreach ($attributes as $key => &$attribute) {
                if (isset($attribute['customizationKey'])) {
                    if (is_array($attribute['customizationKey'])) {
                        foreach ($attribute['customizationKey'] as $path) {
                            $customization_value = self::get_value_by_path(self::$customization_data, explode('.', $path['customizationPath']));
                            if ($customization_value !== null) {
                                self::set_value_by_path($attribute, explode('.', $path['blockPath']), $customization_value);
                            }
                        }
                    } else {
                        $customization_value = self::get_value_by_path(self::$customization_data, explode('.', $attribute['customizationKey']));
                        if ($customization_value !== null) {
                            $attribute['default'] = $customization_value;
                        }
                    }
                }
                self::replace_plugin_url($attribute);
            }
            return $attributes;
        }

        private static function get_value_by_path($array, $path) {
            foreach ($path as $key) {
                if (!isset($array[$key])) {
                    return null;
                }
                $array = $array[$key];
            }
            return $array;
        }

        private static function set_value_by_path(&$array, $path, $value) {
            $current = &$array;
            foreach ($path as $key) {
                if (!isset($current[$key])) {
                    $current[$key] = [];
                }
                $current = &$current[$key];
            }
            $current = $value;
        }

        private static function replace_plugin_url(&$attribute) {
            if (is_array($attribute)) {
                array_walk_recursive($attribute, function(&$item) {
                    if (is_string($item) && strpos($item, 'PLUGIN_URL_PLACEHOLDER') !== false) {
                        $item = str_replace('PLUGIN_URL_PLACEHOLDER', self::$plugin_url, $item);
                    }
                });
            } else if (is_string($attribute) && strpos($attribute, 'PLUGIN_URL_PLACEHOLDER') !== false) {
                $attribute = str_replace('PLUGIN_URL_PLACEHOLDER', self::$plugin_url, $attribute);
            }
        }
    }

    // Initialize the customization helper
    AffiliateX_Customization_Helper::init();
}
