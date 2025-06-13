<?php
/**
 * Header Template
 * Fully dynamic structure for the WDM Custom Header
 */

if (!defined('ABSPATH')) exit;

$options = get_option('wdm_header_options', []);
$menu_items = get_option('wdm_menu_items', []); // ✅ Pull from the correct option

// Fetch and sanitize logo values
$logo_url = esc_url($options['logo_url'] ?? '');
$logo_alt = esc_attr($options['org_name'] ?? 'Site Logo');

$volunteer = [
  'label' => $options['volunteer_text'] ?? 'Volunteer',
  'url' => $options['volunteer_url'] ?? '#volunteer'
];
$donate = [
  'label' => $options['donate_text'] ?? 'Donate',
  'url' => $options['donate_url'] ?? '#donate'
];
$show_search = $options['show_search'] ?? false;
?>

<header class="wdm-main-header" id="wdm-header">
  <div class="wdm-header-container">

    <h1 class="wdm-logo">
      <a class="wdm-logo-link" href="<?php echo esc_url($options['home_url'] ?? '/'); ?>">
        <span class="wdm-screen-reader"><?php echo $logo_alt; ?></span>
        <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="wdm-logo-image">
      </a>
    </h1>

    <div class="wdm-nav">

      <nav class="wdm-nav-secondary" aria-label="Secondary" id="secondary-nav">
        <div class="wdm-utility-nav">
          <ul class="wdm-utility-list is-desktop" role="list">
            <li class="wdm-utility-item">
              <a class="wdm-utility-link" href="#store">Store</a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link" href="#news">News &amp; Stories</a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link is-red" href="#request-help">
                Request Help
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="18" viewBox="0 0 15 18" fill="none" class="icon">
                  <path d="M6.4375 1H13C13.5523 1 14 1.44772 14 2V16C14 16.5523 13.5523 17 13 17H6.4375M1 9H9.25M9.25 9L5.125 4.63636M9.25 9L5.125 13.3636" stroke="#BE2437" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link wdm-utility-link--search" href="#search">
                <span class="wdm-screen-reader">Search</span>
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 15" width="16" height="16" class="search-icon">
                  <circle cx="5.6" cy="5.6" r="4.6" stroke="var(--icon-color, #38444A)" stroke-width="2"/>
                  <path d="M13.293 14.707a1 1 0 0 0 1.415-1.414l-1.415 1.415Zm-5.6-5.6 5.6 5.6 1.415-1.414-5.6-5.6-1.415 1.415Z" fill="var(--icon-color, #38444A)"/>
                </svg>
              </a>
            </li>
          </ul>

          <div class="wdm-utility-buttons">
            <button class="wdm-hamburger-btn" type="button" data-expands="nav" style="display: none;">
              <span class="wdm-screen-reader">Menu</span>
              <div class="wdm-hamburger-icon" aria-hidden="true"><span></span><span></span><span></span></div>
            </button>
            <a href="<?php echo esc_url($volunteer['url']); ?>" class="wdm-utility-btn btn-volunteer is-desktop"><?php echo esc_html($volunteer['label']); ?></a>
            <a href="<?php echo esc_url($donate['url']); ?>" target="_blank" class="wdm-utility-btn btn-donate"><?php echo esc_html($donate['label']); ?></a>
          </div>
        </div>
      </nav>

      <nav class="Header-nav-main Nav-expandable" id="nav" role="navigation" aria-label="Main">
        <div class="Nav-expandable-wrap" style="overflow: hidden;">
          <ul class="Nav-list Nav-primary" role="list">
            <?php foreach ($menu_items as $item): ?>
              <?php
                $text = esc_html($item['text'] ?? '');
                $url = esc_url($item['url'] ?? '#');
                $target = esc_attr($item['target'] ?? '_self');
                $submenu = $item['submenu'] ?? [];
                $has_dropdown = !empty($submenu);
                
                $dropdown_id = sanitize_title($item['text'] ?? uniqid('nav_'));
              ?>
              <li class="Nav-item <?php echo $has_dropdown ? 'has-megadropdown' : ''; ?>">
              <?php if ($has_dropdown): ?>
  <button class="Nav-toggle Nav-link" type="button" data-expands="<?php echo esc_attr($dropdown_id); ?>" aria-haspopup="true" aria-expanded="false">
    <?php echo $text; ?>
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" width="11" height="7" aria-hidden="true">
      <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="currentColor"/>
    </svg>
  </button>

  <?php if (!empty($item['mega_menu']) && $item['mega_menu'] == 1): ?>
<!-- Mega Dropdown -->
<div class="Nav-megaDropdown" id="<?php echo esc_attr($dropdown_id); ?>" aria-hidden="true">
  <div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">

    <!-- Column 1: Title & Description -->
    <div class="Nav-megaDropdown-col is-col-1">
    <div class="Nav-megaDropdown-content">
        <a id="mega-dropdown-title-<?php echo esc_attr($dropdown_id); ?>" class="Nav-megaDropdown-title is-col-1" href="<?php echo esc_url($url); ?>">
          <?php echo esc_html($item['submenu'][0]['text'] ?? ''); ?>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
            <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
            <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
          </svg>
        </a>
        <?php if (!empty($item['submenu'][0]['description'])): ?>
          <p class="Nav-megaDropdown-description"><?php echo wp_kses_post(stripslashes($item['submenu'][0]['description'])); ?></p>
        <?php endif; ?>
      </div>
    </div>


    <!-- Column 2 -->
    <?php if (!empty($item['columns'][0]['title']) || !empty($item['columns'][0]['links'])): ?>
      <div class="Nav-megaDropdown-col is-col-2">
        <?php if (!empty($item['columns'][0]['title'])): ?>
          <p id="mega-dropdown-title-<?php echo esc_attr($dropdown_id); ?>-col2" class="Nav-megaDropdown-header">
            <?php echo esc_html($item['columns'][0]['title']); ?>
          </p>
        <?php endif; ?>
        <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-<?php echo esc_attr($dropdown_id); ?>-col2" role="list">
          <?php foreach ($item['columns'][0]['links'] ?? [] as $link): ?>
            <li class="Nav-megaDropdown-item">
              <a href="<?php echo esc_url($link['url'] ?? '#'); ?>" class="Nav-megaDropdown-link">
                <?php echo esc_html($link['text'] ?? ''); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- Column 3 -->
    <?php if (!empty($item['columns'][1]['title']) || !empty($item['columns'][1]['links'])): ?>
      <div class="Nav-megaDropdown-col is-col-3">
        <?php if (!empty($item['columns'][1]['title'])): ?>
          <p id="mega-dropdown-title-<?php echo esc_attr($dropdown_id); ?>-col3" class="Nav-megaDropdown-header">
            <?php echo esc_html($item['columns'][1]['title']); ?>
          </p>
        <?php endif; ?>
        <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-<?php echo esc_attr($dropdown_id); ?>-col3" role="list">
          <?php foreach ($item['columns'][1]['links'] ?? [] as $link): ?>
            <li class="Nav-megaDropdown-item">
              <a href="<?php echo esc_url($link['url'] ?? '#'); ?>" class="Nav-megaDropdown-link">
                <?php echo esc_html($link['text'] ?? ''); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

  </div>
</div>
  <?php else: ?>
    <!-- Standard Dropdown -->
    <div class="Nav-dropdown" id="<?php echo esc_attr($dropdown_id); ?>" aria-hidden="true">
      <div class="Nav-dropdown-wrap">
        <ul class="Nav-dropdown-list" role="list">
          <?php if (!empty($item['text']) || !empty($item['description'])): ?>
            <li class="Nav-dropdown-parent">
              <a class="Nav-dropdown-title" href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($item['submenu'][0]['text'] ?? ''); ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                  <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                  <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                </svg>
              </a>
              <?php if (!empty($item['submenu'][0]['description'])): ?>
                <p class="Nav-megaDropdown-description"><?php echo wp_kses_post(stripslashes($item['submenu'][0]['description'])); ?></p>
              <?php endif; ?>
            </li>
          <?php endif; ?>

          <?php foreach ($submenu as $index => $sub): ?>
            <?php if ($index === 0) continue; ?>
            <li class="Nav-dropdown-item animate-nav-dropdown-<?php echo $index + 1; ?>">
              <a class="Nav-dropdown-link" href="<?php echo esc_url($sub['url'] ?? '#'); ?>">
                <?php echo esc_html($sub['text'] ?? ''); ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

  <?php endif; ?>
<?php else: ?>
  <a class="Nav-link" href="<?php echo $url; ?>" target="<?php echo $target; ?>">
    <?php echo $text; ?>
  </a>
<?php endif; ?>

              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </nav>

    </div>
  </div>
</header>
