<?php
/**
 * Header Template
 * Fully dynamic structure for the WDM Custom Header
 */

if (!defined("ABSPATH")) {
    exit();
}

$options = get_option("wdm_header_options", []);

$menu_items = get_option("wdm_menu_items", []);

// Fetch and sanitize logo values
$logo_url = esc_url($options["logo_url"] ?? "");
$logo_alt = esc_attr($options["org_name"] ?? "Site Logo");
$scroll_trigger = isset($options["scroll_trigger"])
    ? (int) $options["scroll_trigger"]
    : 100;

$volunteer = [
    "label" => $options["volunteer_text"] ?? "Volunteer",
    "url" => $options["volunteer_url"] ?? "#volunteer",
];
$donate = [
    "label" => $options["donate_text"] ?? "Donate",
    "url" => $options["donate_url"] ?? "#donate",
];
$show_search = $options["show_search"] ?? false;
?>

<?php
$mobile_breakpoint = isset($options['mobile_breakpoint']) ? absint($options['mobile_breakpoint']) : 1024;
?>
<style>
  @media (max-width: <?php echo $mobile_breakpoint; ?>px) {
    .wdm-mobile-menu {
      width: 50%;
    }
    .mobile-only {
      display: flex;
      gap: 0;
    }

    .wdm-mobile-menu-toggle {
      display: flex;
    }

    .wdm-nav-secondary,
    .Header-nav-main {
        display: none !important;
      }
      .wdm-header-container {
      height: auto !important;
    }

    .wdm-header-container {
      justify-content: space-between;
    }
    .wdm-logo-link {
      padding: 10px;
    }
    .wdm-nav {
      display: none !important;
    }

    .wdm-main-header.scrolled .wdm-header-container,
    .wdm-main-header.nav-open .wdm-header-container {
      height: auto !important;
    }

    .emergency-alert-mobile.mobile-only {
      display: block;
      position: fixed;
      bottom: 20px;
      left: 20px;
    }
    .emergency-alert-banner {
      display: none;
    }
  }
</style>

<?php if (
    !empty($options["enable_emergency"]) &&
    $options["enable_emergency"] === "1"
): ?>
  <div class="emergency-alert-banner">
    <div class="emergency-alert-content">
      <?php if (!empty($options["emergency_text"])): ?>
        <div class="alert-text-wrapper">
          <div class="alert-text">
            <span><?php echo esc_html($options["emergency_text"]); ?></span>
            <span><?php echo esc_html($options["emergency_text"]); ?></span>
          </div>
        </div>
      <?php endif; ?>

      <?php if (
          !empty($options["emergency_button_text"]) &&
          !empty($options["emergency_button_url"])
      ): ?>
        <a class="alert-button" href="<?php echo esc_url(
            $options["emergency_button_url"]
        ); ?>">
          <?php echo esc_html($options["emergency_button_text"]); ?>
        </a>
      <?php endif; ?>
    </div>
  </div>
  <div class="emergency-alert-mobile mobile-only">
    <button class="emergency-alert-mobile-btn" type="button">
      <i class="fa-solid fa-exclamation"></i>
    </button>
  </div>
<?php endif; ?>

<header class="wdm-main-header" id="wdm-header" data-scroll-trigger="<?php echo esc_attr(
    $scroll_trigger
); ?>" data-hysteresis="10">

<div class="wdm-header-container">
    <h1 class="wdm-logo">
      <a class="wdm-logo-link" href="<?php echo esc_url(
          $options["home_url"] ?? "/"
      ); ?>">
        <span class="wdm-screen-reader"><?php echo $logo_alt; ?></span>
        <img src="<?php echo $logo_url; ?>" alt="<?php echo $logo_alt; ?>" class="wdm-logo-image">
      </a>
    </h1>

    <div class="wdm-nav">
      <nav class="wdm-nav-secondary" aria-label="Secondary" id="secondary-nav">
        <div class="wdm-utility-nav">
          <?php
          $utility_menu = get_option("wdm_utility_menu", []);

          if (!empty($utility_menu) && is_array($utility_menu)): ?>
            <ul class="wdm-utility-list is-desktop" role="list">
              <?php foreach ($utility_menu as $item):

                  $text = esc_html($item["text"] ?? "");
                  $url = esc_url($item["url"] ?? "#");
                  $target = esc_attr($item["target"] ?? "_self");
                  $icon = trim($item["icon"] ?? "");
                  ?>
                <li class="wdm-utility-item">
                  <a class="wdm-utility-link" href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                    <?php echo $text; ?>
                    <?php if (!empty($icon)): ?>
                      <i class="<?php echo esc_attr(
                          $icon
                      ); ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                  </a>
                </li>
              <?php
              endforeach; ?>
            </ul>
          <?php endif;
          ?>

          <div class="wdm-utility-buttons">
          <button class="wdm-hamburger-btn" type="button" data-expands="nav" style="display: none;">
              <span class="wdm-screen-reader">Menu</span>
              <div class="wdm-hamburger-icon" aria-hidden="true">
                  <span></span><span></span><span></span>
              </div>
          </button>

          <?php
          $utility_buttons =
              get_option("wdm_header_options")["utility_buttons"] ?? [];
          foreach ($utility_buttons as $index => $btn):

              $label = esc_html($btn["label"]);
              $url = esc_url($btn["url"]);
              $color = esc_attr($btn["color"] ?? "#d13a30");
              $classes = "wdm-utility-btn";

              // Apply special classes to first two buttons
              if ($index === 0) {
                  $classes .= " btn-volunteer";
              } elseif ($index === 1) {
                  $classes .= " btn-donate";
              } else {
                  $classes .= " btn-utility-" . ($index + 1);
              }
              ?>
              <a href="<?php echo $url; ?>" class="<?php echo esc_attr($classes); ?>" style="background-color: <?php echo $color; ?>;">
                  <?php echo $label; ?>
              </a>
            <?php
          endforeach;
          ?>  
</div>
      </nav>

      <nav class="Header-nav-main Nav-expandable" id="nav" role="navigation" aria-label="Main">
        <div class="Nav-expandable-wrap" style="overflow: hidden;">
          <ul class="Nav-list Nav-primary" role="list">
            <?php foreach ($menu_items as $item): ?>
              <?php
              $text = esc_html($item["text"] ?? "");
              $url = esc_url($item["url"] ?? "#");
              $target = esc_attr($item["target"] ?? "_self");
              $submenu = $item["submenu"] ?? [];
              $has_dropdown = !empty($submenu);

              $dropdown_id = sanitize_title($item["text"] ?? uniqid("nav_"));
              ?>
              <li class="Nav-item <?php echo $has_dropdown
                  ? "has-megadropdown"
                  : ""; ?>">
              <?php if ($has_dropdown): ?>
                <button class="Nav-toggle Nav-link" type="button" data-expands="<?php echo esc_attr(
                    $dropdown_id
                ); ?>" aria-haspopup="true" aria-expanded="false">
                  <?php echo $text; ?>
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" width="11" height="7" aria-hidden="true">
                    <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="currentColor"/>
                  </svg>
                </button>

                <?php if (
                    !empty($item["mega_menu"]) &&
                    $item["mega_menu"] == 1
                ): ?>
                <!-- Mega Dropdown -->
                <div class="Nav-megaDropdown" id="<?php echo esc_attr(
                    $dropdown_id
                ); ?>" aria-hidden="true">
                  <div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">

                    <!-- Column 1: Title & Description -->
                    <div class="Nav-megaDropdown-col is-col-1">
                    <div class="Nav-megaDropdown-content">
                    <a id="mega-dropdown-title-<?php echo esc_attr(
                        $dropdown_id
                    ); ?>" class="Nav-megaDropdown-title is-col-1" href="<?php echo esc_url($item["submenu"][0]["url"] ?? "#"); ?>">
                          <?php echo esc_html(
                              $item["submenu"][0]["text"] ?? ""
                          ); ?>
                          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                            <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                            <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                          </svg>
                        </a>
                        <?php if (
                            !empty($item["submenu"][0]["description"])
                        ): ?>
                          <p class="Nav-megaDropdown-description"><?php echo wp_kses_post(
                              stripslashes($item["submenu"][0]["description"])
                          ); ?></p>
                        <?php endif; ?>
                      </div>
                    </div>


                    <!-- Column 2 -->
                    <?php if (
                        !empty($item["columns"][0]["title"]) ||
                        !empty($item["columns"][0]["links"])
                    ): ?>
                      <div class="Nav-megaDropdown-col is-col-2">
                        <?php if (!empty($item["columns"][0]["title"])): ?>
                          <p id="mega-dropdown-title-<?php echo esc_attr(
                              $dropdown_id
                          ); ?>-col2" class="Nav-megaDropdown-header">
                            <?php echo esc_html(
                                $item["columns"][0]["title"]
                            ); ?>
                          </p>
                        <?php endif; ?>
                        <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-<?php echo esc_attr(
                            $dropdown_id
                        ); ?>-col2" role="list">
                          <?php foreach (
                              $item["columns"][0]["links"] ?? []
                              as $link
                          ): ?>
                            <li class="Nav-megaDropdown-item">
                              <a href="<?php echo esc_url(
                                  $link["url"] ?? "#"
                              ); ?>" class="Nav-megaDropdown-link">
                                <?php echo esc_html($link["text"] ?? ""); ?>
                              </a>
                            </li>
                          <?php endforeach; ?>
                        </ul>
                      </div>
                    <?php endif; ?>

                    <!-- Column 3 -->
                    <?php if (
                        !empty($item["columns"][1]["title"]) ||
                        !empty($item["columns"][1]["links"])
                    ): ?>
                      <div class="Nav-megaDropdown-col is-col-3">
                        <?php if (!empty($item["columns"][1]["title"])): ?>
                          <p id="mega-dropdown-title-<?php echo esc_attr(
                              $dropdown_id
                          ); ?>-col3" class="Nav-megaDropdown-header">
                            <?php echo esc_html(
                                $item["columns"][1]["title"]
                            ); ?>
                          </p>
                        <?php endif; ?>
                        <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-<?php echo esc_attr(
                            $dropdown_id
                        ); ?>-col3" role="list">
                          <?php foreach (
                              $item["columns"][1]["links"] ?? []
                              as $link
                          ): ?>
                            <li class="Nav-megaDropdown-item">
                              <a href="<?php echo esc_url(
                                  $link["url"] ?? "#"
                              ); ?>" class="Nav-megaDropdown-link">
                                <?php echo esc_html($link["text"] ?? ""); ?>
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
                  <div class="Nav-dropdown" id="<?php echo esc_attr(
                      $dropdown_id
                  ); ?>" aria-hidden="true">
                    <div class="Nav-dropdown-wrap">
                      <ul class="Nav-dropdown-list" role="list">
                        <?php if (
                            !empty($item["text"]) ||
                            !empty($item["description"])
                        ): ?>
                          <li class="Nav-dropdown-parent">
                            <a class="Nav-dropdown-title" href="<?php echo esc_url($item["submenu"][0]["url"] ?? "#"); ?>">
                              <?php echo esc_html(
                                  $item["submenu"][0]["text"] ?? ""
                              ); ?>
                              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                                <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                                <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                              </svg>
                            </a>
                            <?php if (
                                !empty($item["submenu"][0]["description"])
                            ): ?>
                              <p class="Nav-megaDropdown-description"><?php echo wp_kses_post(
                                  stripslashes(
                                      $item["submenu"][0]["description"]
                                  )
                              ); ?></p>
                            <?php endif; ?>
                          </li>
                        <?php endif; ?>

                        <?php foreach ($submenu as $index => $sub): ?>
                          <?php if ($index === 0) {
                              continue;
                          } ?>
                          <li class="Nav-dropdown-item animate-nav-dropdown-<?php echo $index +
                              1; ?>">
                            <a class="Nav-dropdown-link" href="<?php echo esc_url(
                                $sub["url"] ?? "#"
                            ); ?>">
                              <?php echo esc_html($sub["text"] ?? ""); ?>
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

  <!-- Mobile Menu Toggle -->
  <div class="mobile-header-actions mobile-only">
    <?php foreach ($utility_buttons as $btn):

        if (!isset($btn["featured"]) || $btn["featured"] !== "1") {
            continue;
        }
        $label = esc_html($btn["label"]);
        $url = esc_url($btn["url"]);
        $color = esc_attr($btn["color"] ?? "#d13a30");
        ?>
      <a href="<?php echo $url; ?>" class="wdm-utility-btn" style="background-color: <?php echo $color; ?>;">
        <?php echo $label; ?>
      </a>
    <?php break;
    endforeach; ?>

    <button class="wdm-mobile-menu-toggle" type="button" aria-label="Open Mobile Menu">
      <span class="wdm-screen-reader">Menu</span>
      <div class="wdm-hamburger-icon" aria-hidden="true">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </button>
  </div>

  <div class="wdm-mobile-menu-overlay" id="mobileMenuOverlay" aria-hidden="true"></div>
  <!-- Mobile Menu Panel -->
  <div class="wdm-mobile-menu" id="mobileMenu" aria-hidden="true">
    <div class="mobile-menu-header">
      <span class="mobile-menu-title">Navigation Menu</span>
      <button class="wdm-mobile-menu-close" type="button" aria-label="Close Mobile Menu">
        <span class="wdm-screen-reader">Close Menu</span>
        <div class="wdm-close-icon" aria-hidden="true">
          <span></span>
          <span></span>
        </div>
      </button>
    </div>
    <div class="mobile-menu-inner">
      <!-- Main Menu Items -->
      <ul class="mobile-nav-list">
        <?php foreach ($menu_items as $item): ?>
          <?php
          $text = esc_html($item["text"] ?? "");
          $url = esc_url($item["url"] ?? "#");
          $target = esc_attr($item["target"] ?? "_self");
          $submenu = $item["submenu"] ?? [];
          $columns = $item["columns"] ?? [];
          $mega_menu = !empty($item["mega_menu"]) && $item["mega_menu"] === "1";
          $has_dropdown =
              (!empty($submenu) && count($submenu) > 1) || !empty($columns);
          ?>
          <li class="mobile-nav-item<?php echo $has_dropdown
              ? " has-dropdown"
              : ""; ?>">
            <div class="mobile-nav-link-wrap">
              <a href="<?php echo $url; ?>" target="<?php echo $target; ?>">
                <?php echo $text; ?>
              </a>
              <?php if ($has_dropdown): ?>
                <button class="mobile-submenu-toggle" aria-expanded="false" aria-label="Toggle submenu">
                  <span class="arrow"></span>
                </button>
              <?php endif; ?>
            </div>

            <?php if (!$mega_menu && count($submenu) > 1): ?>
              <!-- Standard submenu -->
              <ul class="mobile-submenu" hidden>
                <?php foreach (array_slice($submenu, 1) as $sub): ?>
                  <li>
                    <a href="<?php echo esc_url(
                        $sub["url"] ?? "#"
                    ); ?>" target="<?php echo esc_attr($sub["target"] ?? "_self"); ?>">
                      <?php echo esc_html($sub["text"] ?? ""); ?>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>

            <?php elseif ($mega_menu && !empty($columns)): ?>
              <!-- Mega menu submenu -->
              <ul class="mobile-submenu" hidden>
                <?php foreach ($columns as $col): ?>
                  <?php if (!empty($col["title"])): ?>
                    <li class="mobile-submenu-section-header">
                      <?php echo esc_html($col["title"]); ?>
                    </li>
                  <?php endif; ?>

                  <?php foreach ($col["links"] ?? [] as $link): ?>
                    <li>
                      <a href="<?php echo esc_url(
                          $link["url"] ?? "#"
                      ); ?>" target="<?php echo esc_attr($link["target"] ?? "_self"); ?>">
                        <?php echo esc_html($link["text"] ?? ""); ?>
                      </a>
                    </li>
                  <?php endforeach; ?>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Utility Items -->
      <ul class="mobile-utility-list">
        <?php foreach ($utility_menu as $item): ?>
          <li>
            <a href="<?php echo esc_url(
                $item["url"] ?? "#"
            ); ?>" target="<?php echo esc_attr($item["target"] ?? "_self"); ?>">
              <?php echo esc_html($item["text"] ?? ""); ?>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>

      <!-- Utility Buttons -->
      <div class="mobile-utility-buttons">
        <?php foreach ($utility_buttons as $index => $btn): ?>
          <a href="<?php echo esc_url(
              $btn["url"]
          ); ?>" class="mobile-utility-btn" style="background-color: <?php echo esc_attr($btn["color"] ?? "#d13a30"); ?>">
            <?php echo esc_html($btn["label"]); ?>
          </a>
        <?php endforeach; ?>
      </div>

    </div>
  </div>

  </div>

</header>
<?php if (
    !empty($options["enable_emergency"]) &&
    $options["enable_emergency"] === "1"
): ?>
  <div class="emergency-alert-modal-overlay" style="display: none;">
    <div class="emergency-alert-modal">
      <button class="emergency-alert-modal-close" aria-label="Close">&times;</button>
      <div class="emergency-alert-modal-content">
        <?php if (!empty($options["emergency_text"])): ?>
          <p><?php echo esc_html($options["emergency_text"]); ?></p>
        <?php endif; ?>
        <?php if (!empty($options["emergency_button_text"]) && !empty($options["emergency_button_url"])): ?>
          <a href="<?php echo esc_url($options["emergency_button_url"]); ?>" class="alert-button">
            <?php echo esc_html($options["emergency_button_text"]); ?>
          </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
<?php endif; ?>