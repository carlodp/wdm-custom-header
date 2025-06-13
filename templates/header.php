<?php
/**
 * Header Template
 * Fully dynamic structure for the WDM Custom Header
 */

if (!defined('ABSPATH')) exit;

$options = get_option('wdm_header_options', []);
$menu_items = get_option('wdm_menu_items', []); // ✅ Updated from wdm_header_menu_data

$logo_url = esc_url($logo_url ?? '');
$logo_alt = esc_attr($logo_alt ?? '');
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
      <a class="wdm-logo-link" href="/">
        <span class="wdm-screen-reader"><?php echo $logo_alt; ?></span>
        <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="wdm-logo-image">
      </a>
    </h1>

    <div class="wdm-nav">

      <nav class="wdm-nav-secondary" aria-label="Secondary" id="secondary-nav">
        <div class="wdm-utility-nav">
          <ul class="wdm-utility-list is-desktop" role="list">
            <li class="wdm-utility-item">
              <a class="wdm-utility-link" href="#store">
                Store
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13.125" viewBox="0 0 16 14" fill="none" class="icon">
                  <path d="M4.03613 3.10352H14.194C14.7988 3.10352 15.2651 3.6362 15.1852 4.23568L14.529 9.15709H4.4397L4.03613 3.10352Z" fill="#38444A"/>
                  <path d="M0 0.683594H4.64108L4.73019 1.89431H0V0.683594Z" fill="#38444A"/>
                  <circle cx="4.43851" cy="12.3858" r="1.61429" fill="#38444A"/>
                  <circle cx="13.7207" cy="12.3858" r="1.61429" fill="#38444A"/>
                  <path d="M2.82422 1.08789H4.64029L5.64922 9.96647H3.83315L2.82422 1.08789Z" fill="#38444A"/>
                  <path d="M3.83099 9.9668H14.1259V11.5811H4.03658L3.83099 9.9668Z" fill="#38444A"/>
                </svg>
              </a>
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
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 15 15" width="16" height="16" class="search-icon">
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
              <li class="Nav-item <?php echo (!empty($item['submenu']) ? 'has-megadropdown' : ''); ?>">
                <?php if (!empty($item['submenu'])): ?>
                  <?php $dropdown_id = sanitize_title($item['text'] ?? uniqid('nav_')); ?>
                  <button class="Nav-toggle Nav-link" type="button" data-expands="<?php echo esc_attr($dropdown_id); ?>" aria-haspopup="true" aria-expanded="false">
                    <?php echo esc_html($item['text']); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" width="11" height="7" aria-hidden="true"><path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="currentColor"/></svg>
                  </button>
                  <div class="Nav-megaDropdown" id="<?php echo esc_attr($dropdown_id); ?>" aria-hidden="true">
                    <div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">
                      <div class="Nav-megaDropdown-col is-col-1">
                        <ul class="Nav-megaDropdown-list" role="list">
                          <?php foreach ($item['submenu'] as $sub): ?>
                            <li class="Nav-megaDropdown-item">
                              <a class="Nav-megaDropdown-link" href="<?php echo esc_url($sub['url']); ?>" target="<?php echo esc_attr($sub['target']); ?>">
                                <?php echo esc_html($sub['text']); ?>
                                <?php if (!empty($sub['description'])): ?>
                                  <div class="Nav-megaDropdown-description"><?php echo esc_html($sub['description']); ?></div>
                                <?php endif; ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    </div>
                  </div>
                <?php else: ?>
                  <a class="Nav-link" href="<?php echo esc_url($item['url']); ?>" target="<?php echo esc_attr($item['target']); ?>">
                    <?php echo esc_html($item['text']); ?>
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
