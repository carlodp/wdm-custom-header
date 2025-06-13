<?php
/**
 * Dynamic WDM Custom Header Template
 * Uses menu data from admin settings
 */

// Get dynamic menu items from admin settings
$menu_items = get_option('wdm_menu_items', array());

// Default fallback if no menu items configured
if (empty($menu_items)) {
    $menu_items = array(
        array(
            'text' => 'Home',
            'url' => '#',
            'target' => '_self',
            'submenu' => array()
        ),
        array(
            'text' => 'About',
            'url' => '#about',
            'target' => '_self',
            'submenu' => array(
                array(
                    'text' => 'Our Mission',
                    'url' => '#mission',
                    'target' => '_self',
                    'description' => 'Learn about our rescue mission and values'
                ),
                array(
                    'text' => 'Our Team',
                    'url' => '#team',
                    'target' => '_self',
                    'description' => 'Meet our dedicated rescue team'
                ),
                array(
                    'text' => 'Success Stories',
                    'url' => '#stories',
                    'target' => '_self',
                    'description' => 'Read inspiring rescue success stories'
                )
            )
        ),
        array(
            'text' => 'Services',
            'url' => '#services',
            'target' => '_self',
            'submenu' => array(
                array(
                    'text' => 'Animal Rescue',
                    'url' => '#rescue',
                    'target' => '_self',
                    'description' => 'Emergency animal rescue operations'
                ),
                array(
                    'text' => 'Adoption',
                    'url' => '#adoption',
                    'target' => '_self',
                    'description' => 'Find your perfect companion'
                ),
                array(
                    'text' => 'Volunteer',
                    'url' => '#volunteer',
                    'target' => '_self',
                    'description' => 'Join our volunteer program'
                )
            )
        ),
        array(
            'text' => 'Contact',
            'url' => '#contact',
            'target' => '_self',
            'submenu' => array()
        )
    );
}
?>
<header class="Header" id="Header" data-header="">
  <div class="Header-wrap">
    
    <!-- Utility Navigation -->
    <nav class="Header-nav-utility UtilityNav" role="navigation" aria-label="Utility">
      <div class="UtilityNav-wrap">
        <div class="UtilityNav-buttons">
          <a href="#volunteer" class="UtilityNav-buttons-btn btn-solid btn-solid-gray">Volunteer</a>
          <a href="#donate" class="UtilityNav-buttons-btn btn-solid btn-solid-orange">Donate</a>
        </div>
        
        <div class="UtilityNav-search">
          <button class="UtilityNav-search-toggle btn-icon" data-toggle="search" type="button" aria-label="Search" aria-expanded="false">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
              <path d="M19.03 19.53a.75.75 0 0 1-1.06 0l-4.47-4.47a7.5 7.5 0 1 1 1.06-1.06l4.47 4.47a.75.75 0 0 1 0 1.06zM9.5 15.5a6 6 0 1 0 0-12 6 6 0 0 0 0 12z" fill="var(--icon-color, currentColor)"></path>
            </svg>
          </button>
        </div>
      </div>
    </nav>
    
    <!-- Main Header -->
    <div class="Header-main">
      
      <!-- Logo -->
      <div class="Header-logo">
        <a href="#home" class="wdm-logo-link">
          <img src="https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png" alt="Greybull Rescue" class="wdm-logo-image">
        </a>
      </div>
      
      <!-- Mobile Toggle -->
      <button class="Header-nav-toggle Nav-toggle-mobile" data-toggle="nav" type="button" aria-label="Open navigation" aria-expanded="false">
        <div class="wdm-hamburger">
          <span class="wdm-hamburger-line"></span>
          <span class="wdm-hamburger-line"></span>
          <span class="wdm-hamburger-line"></span>
        </div>
      </button>
      
      <!-- Main Navigation -->
      <nav class="Header-nav-main Nav-expandable" id="nav" role="navigation" aria-label="Main">
        <div class="Nav-expandable-wrap" style="overflow: hidden;">
          <ul class="Nav-list Nav-primary" role="list">
            
            <?php foreach ($menu_items as $index => $item):
                $text = isset($item['text']) ? esc_html($item['text']) : '';
                $url = isset($item['url']) ? esc_url($item['url']) : '#';
                $target = isset($item['target']) ? $item['target'] : '_self';
                $submenu = isset($item['submenu']) && is_array($item['submenu']) ? $item['submenu'] : array();
                $has_submenu = !empty($submenu);
                $dropdown_id = 'nav-' . $index;
                
                if (empty($text)) continue;
            ?>
            
            <li class="Nav-item <?php echo $has_submenu ? 'has-megadropdown' : ''; ?>">
              <?php if ($has_submenu): ?>
                <button class="Nav-toggle Nav-link" data-expands="<?php echo $dropdown_id; ?>" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">
                  <?php echo $text; ?>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">
                    <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>
                  </svg>
                </button>
                
                <?php if (count($submenu) > 3): ?>
                <!-- Mega dropdown for items with more than 3 submenu items -->
                <div class="Nav-megaDropdown" id="<?php echo $dropdown_id; ?>" aria-hidden="true">
                  <div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">
                    
                    <?php if (!empty($url) && $url !== '#'): ?>
                    <div class="Nav-megaDropdown-col is-col-1">
                      <div class="Nav-megaDropdown-content">
                        <a class="Nav-megaDropdown-title is-col-1" href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                          <?php echo $text; ?>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                            <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                            <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                          </svg>
                        </a>
                      </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="Nav-megaDropdown-col <?php echo (!empty($url) && $url !== '#') ? 'is-col-2' : 'is-col-1'; ?>">
                      <ul class="Nav-megaDropdown-list" role="list">
                        <?php foreach ($submenu as $sub_item):
                          $sub_text = isset($sub_item['text']) ? esc_html($sub_item['text']) : '';
                          $sub_url = isset($sub_item['url']) ? esc_url($sub_item['url']) : '#';
                          $sub_target = isset($sub_item['target']) ? $sub_item['target'] : '_self';
                          $sub_description = isset($sub_item['description']) ? esc_html($sub_item['description']) : '';
                          
                          if (empty($sub_text)) continue;
                        ?>
                        <li class="Nav-megaDropdown-item">
                          <a href="<?php echo $sub_url; ?>" target="<?php echo $sub_target; ?>" class="Nav-megaDropdown-link">
                            <?php echo $sub_text; ?>
                          </a>
                          <?php if (!empty($sub_description)): ?>
                          <p class="Nav-megaDropdown-description"><?php echo $sub_description; ?></p>
                          <?php endif; ?>
                        </li>
                        <?php endforeach; ?>
                      </ul>
                    </div>
                    
                  </div>
                </div>
                <?php else: ?>
                <!-- Regular dropdown for items with 3 or fewer submenu items -->
                <div class="Nav-dropdown" id="<?php echo $dropdown_id; ?>" aria-hidden="true">
                  <div class="Nav-dropdown-wrap">
                    <ul class="Nav-dropdown-list" role="list">
                      <?php if (!empty($url) && $url !== '#'): ?>
                      <li class="Nav-dropdown-parent">
                        <a class="Nav-dropdown-title" href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                          <?php echo $text; ?>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                            <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                            <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                          </svg>
                        </a>
                        <?php if (count($submenu) === 1 && !empty($submenu[0]['description'])): ?>
                        <p class="Nav-dropdown-description">
                          <?php echo esc_html($submenu[0]['description']); ?>
                        </p>
                        <?php endif; ?>
                      </li>
                      <?php endif; ?>
                      
                      <?php foreach ($submenu as $anim_index => $sub_item):
                        $sub_text = isset($sub_item['text']) ? esc_html($sub_item['text']) : '';
                        $sub_url = isset($sub_item['url']) ? esc_url($sub_item['url']) : '#';
                        $sub_target = isset($sub_item['target']) ? $sub_item['target'] : '_self';
                        
                        if (empty($sub_text)) continue;
                      ?>
                      <li class="Nav-dropdown-item animate-nav-dropdown-<?php echo $anim_index + 1; ?>">
                        <a class="Nav-dropdown-link" href="<?php echo $sub_url; ?>" target="<?php echo $sub_target; ?>"><?php echo $sub_text; ?></a>
                      </li>
                      <?php endforeach; ?>
                    </ul>
                  </div>
                </div>
                <?php endif; ?>
              <?php else: ?>
                <a class="Nav-link" href="<?php echo $url; ?>" target="<?php echo $target; ?>" role="menuitem"><?php echo $text; ?></a>
              <?php endif; ?>
            </li>
            
            <?php endforeach; ?>
            
          </ul>
        </div>
      </nav>
      
    </div>
    
    <!-- Search Panel -->
    <div class="Header-search" id="search" aria-hidden="true">
      <div class="Header-search-wrap">
        <form class="Header-search-form" role="search" action="#" method="get">
          <input type="search" name="q" placeholder="Search..." class="Header-search-input" />
          <button type="submit" class="Header-search-submit" aria-label="Submit search">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
              <path d="M19.03 19.53a.75.75 0 0 1-1.06 0l-4.47-4.47a7.5 7.5 0 1 1 1.06-1.06l4.47 4.47a.75.75 0 0 1 0 1.06zM9.5 15.5a6 6 0 1 0 0-12 6 6 0 0 0 0 12z" fill="var(--icon-color, currentColor)"></path>
            </svg>
          </button>
        </form>
        <button class="Header-search-close" data-toggle="search" type="button" aria-label="Close search">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
            <path d="M18.3 5.71a.996.996 0 0 0-1.41 0L12 10.59 7.11 5.7A.996.996 0 1 0 5.7 7.11L10.59 12 5.7 16.89a.996.996 0 1 0 1.41 1.41L12 13.41l4.89 4.89a.996.996 0 0 0 1.41-1.41L13.41 12l4.89-4.89c.38-.38.38-1.02 0-1.4z" fill="var(--icon-color, currentColor)"></path>
          </svg>
        </button>
      </div>
    </div>
    
  </div>
</header>