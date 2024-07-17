<?php
namespace Dvpz\Options;
use StoutLogic\AcfBuilder\FieldsBuilder;
use function Inpsyde\Wonolog\bootstrap;

class CsgOptions {
    const CSG_OPTION_PAGE_SLUG = 'site-generator-settings';
    const OPTKEY_ROOT_FOLDER = 'csg_root_folder';
    const OPTKEY_SUBDIR_PREFIX = 'csg_subdir_prefix';
    const OPTKEY_URL_PLACEHOLDER = 'csg_url_placeholder';

    function __construct()
    {
        bootstrap();
        add_action('plugin_loaded', function () {
            if(class_exists('acf')) {
                acf_add_options_page(array(
                    'page_title'    => __('Site Generator Settings'),
                    'menu_title'    => __('Site Generator Settings', 'csg'),
                    'menu_slug'     => self::CSG_OPTION_PAGE_SLUG,
                    'capability'    => 'manage_options',
                    'icon_url'      => 'dashicons-rest-api',
                    'redirect'      => false
                ));

                $csgoption = new FieldsBuilder('csg_option');
                $csgoption
                    ->addText(self::OPTKEY_ROOT_FOLDER, [
                        'key' => self::OPTKEY_ROOT_FOLDER,
                        'label' => __('Root folder name', 'csg'),
                        'required' => true,
                        'instructions' => __('Name of folder that new generated sites will be stored, only folder name allowed', 'csg')
                    ])
                    ->addText(self::OPTKEY_SUBDIR_PREFIX, [
                        'key' => self::OPTKEY_SUBDIR_PREFIX,
                        'label' => __('Subdir prefix', 'csg'),
                        'required' => true,
                        'instructions' => __('Prefix for subdir contain new generated site', 'csg')
                    ])
                    ->addText(self::OPTKEY_URL_PLACEHOLDER, [
                        'key' => self::OPTKEY_URL_PLACEHOLDER,
                        'label' => __('URL placeholder', 'csg'),
                        'required' => true,
                        'instructions' => __('{PLACE_HOLDER} must be included in the url, eg:. https://{PLACE_HOLDER}.domain.com', 'csg')
                    ])
                    ->setLocation('options_page', '==', self::CSG_OPTION_PAGE_SLUG);

                add_action('acf/init', function() use ($csgoption) {
                   acf_add_local_field_group($csgoption->build());
                });
            }
        });
    }
}

