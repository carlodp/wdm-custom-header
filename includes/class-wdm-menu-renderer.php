<?php
/**
 * WDM Menu Renderer Class
 * Handles dynamic menu rendering from admin settings
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Menu_Renderer {
    
    /**
     * Render navigation menu from admin settings
     */
    public static function render_navigation_menu() {
        $menu_items = get_option('wdm_header_menu_items', self::get_default_menu_structure());
        
        if (!is_array($menu_items) || empty($menu_items)) {
            $menu_items = self::get_default_menu_structure();
        }
        
        echo '<ul class="Nav-list Nav-primary" role="list">';
        
        // Mobile volunteer button
        echo '<li class="Nav-item is-mobile">
                <a href="#volunteer" class="UtilityNav-buttons-btn btn-solid btn-solid-gray">Volunteer</a>
              </li>';
        
        foreach ($menu_items as $index => $item) {
            self::render_menu_item($item, $index);
        }
        
        echo '</ul>';
    }
    
    /**
     * Render individual menu item
     */
    private static function render_menu_item($item, $index) {
        $label = isset($item['label']) ? esc_html($item['label']) : '';
        $type = isset($item['type']) ? $item['type'] : 'dropdown';
        $nav_id = 'nav-' . ($index + 150);
        
        if (empty($label)) return;
        
        $has_dropdown_class = ($type === 'megamenu') ? 'has-megadropdown' : '';
        
        echo '<li class="Nav-item ' . $has_dropdown_class . '">';
        
        if ($type === 'link') {
            $url = isset($item['url']) ? esc_url($item['url']) : '#';
            echo '<a class="Nav-link" href="' . $url . '">' . $label . '</a>';
        } else {
            echo '<button class="Nav-toggle Nav-link" data-expands="' . $nav_id . '" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">';
            echo $label;
            echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">';
            echo '<path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>';
            echo '</svg>';
            echo '</button>';
            
            if ($type === 'megamenu') {
                self::render_megamenu($item, $nav_id);
            } else {
                self::render_dropdown($item, $nav_id);
            }
        }
        
        echo '</li>';
    }
    
    /**
     * Render megamenu dropdown
     */
    private static function render_megamenu($item, $nav_id) {
        echo '<div class="Nav-megaDropdown" id="' . $nav_id . '" aria-hidden="true">';
        echo '<div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">';
        
        // For now, render default megamenu structure
        // This can be expanded later for full customization
        self::render_default_megamenu_content();
        
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Render dropdown menu
     */
    private static function render_dropdown($item, $nav_id) {
        echo '<div class="Nav-dropdown" id="' . $nav_id . '" aria-hidden="true">';
        echo '<div class="Nav-dropdown-wrap">';
        echo '<ul class="Nav-dropdown-list" role="list">';
        
        // Render dropdown title if available
        $label = isset($item['label']) ? esc_html($item['label']) : '';
        echo '<li class="Nav-dropdown-parent">';
        echo '<a class="Nav-dropdown-title" href="#about-us">';
        echo $label;
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">';
        echo '<circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>';
        echo '<path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>';
        echo '</svg>';
        echo '</a>';
        echo '<p class="Nav-dropdown-description">';
        echo 'Our unwavering commitment is to build resiliency for vulnerable communities across the globe.';
        echo '</p>';
        echo '</li>';
        
        // Render dropdown items
        $dropdown_items = array(
            array('label' => 'Leadership & Board', 'url' => '#leadership-and-board'),
            array('label' => 'Partners', 'url' => '#partners'),
            array('label' => 'Careers', 'url' => '#careers'),
            array('label' => 'News & Stories', 'url' => '#news-stories'),
            array('label' => 'Contact Us', 'url' => '#contact-us')
        );
        
        foreach ($dropdown_items as $index => $dropdown_item) {
            echo '<li class="Nav-dropdown-item animate-nav-dropdown-' . ($index + 1) . '">';
            echo '<a class="Nav-dropdown-link" href="' . esc_url($dropdown_item['url']) . '">' . esc_html($dropdown_item['label']) . '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
        echo '</div>';
    }
    
    /**
     * Render default megamenu content
     */
    private static function render_default_megamenu_content() {
        echo '<div class="Nav-megaDropdown-col is-col-1">';
        echo '<div class="Nav-megaDropdown-content">';
        echo '<a id="mega-dropdown-title-684a8ec5a1110" class="Nav-megaDropdown-title is-col-1" href="#how-we-serve">';
        echo 'How We Serve';
        echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">';
        echo '<circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>';
        echo '<path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>';
        echo '</svg>';
        echo '</a>';
        echo '<p class="Nav-megaDropdown-description">';
        echo 'Service, to us, is a mindset. It\'s a resolve—a beacon of light amidst the fog of chaos—to bring equitable relief to vulnerable communities before, during, and after a disaster strikes.';
        echo '</p>';
        echo '</div>';
        echo '</div>';
        
        echo '<div class="Nav-megaDropdown-col is-col-2">';
        echo '<p id="mega-dropdown-title-684a8ec5a118e" class="Nav-megaDropdown-header">';
        echo 'What We Do';
        echo '</p>';
        echo '<ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-684a8ec5a118e" role="list">';
        
        $megamenu_items = array(
            array('label' => 'Disaster Response', 'url' => '#disaster-response'),
            array('label' => 'Long Term Recovery', 'url' => '#long-term-recovery'),
            array('label' => 'International', 'url' => '#international')
        );
        
        foreach ($megamenu_items as $index => $mega_item) {
            echo '<li class="Nav-megaDropdown-item animate-nav-megadropdown-' . ($index + 1) . '">';
            echo '<a class="Nav-megaDropdown-link" href="' . esc_url($mega_item['url']) . '">' . esc_html($mega_item['label']) . '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
        
        echo '<div class="Nav-megaDropdown-col is-col-3">';
        echo '<p id="mega-dropdown-title-684a8ec5a11dc" class="Nav-megaDropdown-header">';
        echo 'Where We Work';
        echo '</p>';
        echo '<ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-684a8ec5a11dc" role="list">';
        
        $location_items = array(
            array('label' => 'Domestic Operations', 'url' => '#domestic-operations'),
            array('label' => 'International Work', 'url' => '#international-work')
        );
        
        foreach ($location_items as $index => $location_item) {
            echo '<li class="Nav-megaDropdown-item animate-nav-megadropdown-' . ($index + 4) . '">';
            echo '<a class="Nav-megaDropdown-link" href="' . esc_url($location_item['url']) . '">' . esc_html($location_item['label']) . '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
    
    /**
     * Get default menu structure
     */
    private static function get_default_menu_structure() {
        return array(
            array(
                'label' => 'How We Serve',
                'type' => 'megamenu'
            ),
            array(
                'label' => 'How to Get Involved',
                'type' => 'dropdown'
            ),
            array(
                'label' => 'Ways to Give',
                'type' => 'dropdown'
            ),
            array(
                'label' => 'About Grey Bull',
                'type' => 'dropdown'
            )
        );
    }
}