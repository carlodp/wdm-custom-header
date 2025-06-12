<?php
/**
 * Header Template
 * HTML structure for the WDM Custom Header
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<header class="wdm-main-header" id="wdm-header">
  <div class="wdm-header-container">
    
    <h1 class="wdm-logo">
      <a class="wdm-logo-link" href="/">
        <span class="wdm-screen-reader">WDM Header</span>
        <img src="https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png" alt="Greybull Rescue" class="wdm-logo-image">
      </a>
    </h1>
        
    <div class="wdm-nav">
      
      <nav class="wdm-nav-secondary" aria-label="Secondary" id="secondary-nav">
        
        

        <div class="wdm-utility-nav">
          <ul class="wdm-utility-list is-desktop" role="list">
            <li class="wdm-utility-item">
              <a class="wdm-utility-link" href="#store">
                Store
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13.125" viewBox="0 0 16 14" fill="none" focusable="false" aria-hidden="true" class="icon">
                  <path d="M4.03613 3.10352H14.194C14.7988 3.10352 15.2651 3.6362 15.1852 4.23568L14.529 9.15709H4.4397L4.03613 3.10352Z" fill="#38444A"></path>
                  <path d="M0 0.683594H4.64108L4.73019 1.89431H0V0.683594Z" fill="#38444A"></path>
                  <circle cx="4.43851" cy="12.3858" r="1.61429" fill="#38444A"></circle>
                  <circle cx="13.7207" cy="12.3858" r="1.61429" fill="#38444A"></circle>
                  <path d="M2.82422 1.08789H4.64029L5.64922 9.96647H3.83315L2.82422 1.08789Z" fill="#38444A"></path>
                  <path d="M3.83099 9.9668H14.1259V11.5811H4.03658L3.83099 9.9668Z" fill="#38444A"></path>
                </svg>
              </a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link" href="#news">
                News &amp; Stories
              </a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link is-red" href="#request-help">
                Request Help
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="18" viewBox="0 0 15 18" fill="none" focusable="false" aria-hidden="true" class="icon">
                  <path d="M6.4375 1H13C13.5523 1 14 1.44772 14 2V16C14 16.5523 13.5523 17 13 17H6.4375M1 9H9.25M9.25 9L5.125 4.63636M9.25 9L5.125 13.3636" stroke="#BE2437" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </a>
            </li>
            <li class="wdm-utility-item">
              <a class="wdm-utility-link wdm-utility-link--search" href="#search">
                <span class="wdm-screen-reader">Search</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 15 15" focusable="false" aria-hidden="true" width="16" height="16" class="search-icon">
                  <circle cx="5.6" cy="5.6" r="4.6" stroke="var(--icon-color, #38444A)" stroke-width="2"></circle>
                  <path d="M13.293 14.707a1 1 0 0 0 1.415-1.414l-1.415 1.415Zm-5.6-5.6 5.6 5.6 1.415-1.414-5.6-5.6-1.415 1.415Z" fill="var(--icon-color, #38444A)"></path>
                </svg>
              </a>
            </li>
          </ul>

          <div class="wdm-utility-buttons">
            <button class="wdm-hamburger-btn" type="button" data-expands="nav" style="display: none;">
              <span class="wdm-screen-reader">Menu</span>
              <div class="wdm-hamburger-icon" aria-hidden="true">
                <span></span>
                <span></span>
                <span></span>
              </div>
            </button>
            <a href="#volunteer" class="wdm-utility-btn btn-volunteer is-desktop">VOLUNTEER</a>
            <a href="#donate" target="_blank" class="wdm-utility-btn btn-donate">DONATE</a>
          </div>
        </div>
      </nav>
      
      <nav class="Header-nav-main Nav-expandable" id="nav" role="navigation" aria-label="Main">
        <div class="Nav-expandable-wrap" style="overflow: hidden;">
          <ul class="Nav-list Nav-primary" role="list">
            
            <li class="Nav-item is-mobile">
              <a href="#volunteer" class="UtilityNav-buttons-btn btn-solid btn-solid-gray">Volunteer</a>
            </li>
            
            <li class="Nav-item has-megadropdown">
              <button class="Nav-toggle Nav-link" data-expands="nav-150" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">
                How We Serve
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">
                  <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>
                </svg>
              </button>
              
              <div class="Nav-megaDropdown" id="nav-150" aria-hidden="true">
                <div class="Nav-megaDropdown-wrapper Nav-megaDropdown-wrap">
                  <div class="Nav-megaDropdown-col is-col-1">
                    <div class="Nav-megaDropdown-content">
                      <a id="mega-dropdown-title-684a8ec5a1110" class="Nav-megaDropdown-title is-col-1" href="#how-we-serve">
                        How We Serve
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                          <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                          <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                        </svg>
                      </a>
                      <p class="Nav-megaDropdown-description">
                        Service, to us, is a mindset. It's a resolve—a beacon of light amidst the fog of chaos—to bring equitable relief to vulnerable communities before, during, and after a disaster strikes.
                      </p>
                    </div>
                  </div>
                  
                  <div class="Nav-megaDropdown-col is-col-2">
                    <p id="mega-dropdown-title-684a8ec5a118e" class="Nav-megaDropdown-header">
                      What We Do
                    </p>
                    <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-684a8ec5a118e" role="list">
                      <li class="Nav-megaDropdown-item">
                        <a href="#disaster-response" class="Nav-megaDropdown-link">Disaster Response</a>
                      </li>
                      <li class="Nav-megaDropdown-item">
                        <a href="#long-term-recovery" class="Nav-megaDropdown-link">Long Term Recovery</a>
                      </li>
                      <li class="Nav-megaDropdown-item">
                        <a href="#international" class="Nav-megaDropdown-link">International</a>
                      </li>
                    </ul>
                  </div>
                  
                  <div class="Nav-megaDropdown-col is-col-3">
                    <p id="mega-dropdown-title-684a8ec5a11a1" class="Nav-megaDropdown-header">
                      Where We Work
                    </p>
                    <ul class="Nav-megaDropdown-list" aria-labelledby="mega-dropdown-title-684a8ec5a11a1" role="list">
                      <li class="Nav-megaDropdown-item">
                        <a href="#domestic-operations" class="Nav-megaDropdown-link">Domestic Operations</a>
                      </li>
                      <li class="Nav-megaDropdown-item">
                        <a href="#international-work" class="Nav-megaDropdown-link">International Work</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </li>
            
            <li class="Nav-item">
              <button class="Nav-toggle Nav-link" data-expands="nav-394" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">
                How To Get Involved
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">
                  <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>
                </svg>
              </button>
              
              <div class="Nav-dropdown" id="nav-394" aria-hidden="true">
                <div class="Nav-dropdown-wrap">
                  <ul class="Nav-dropdown-list" role="list">
                    <li class="Nav-dropdown-parent">
                      <a class="Nav-dropdown-title" href="#how-to-get-involved">
                        How To Get Involved
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                          <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                          <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                        </svg>
                      </a>
                      <p class="Nav-dropdown-description">
                        Grey Bull is powered by people. There are countless ways to get involved, so pick your path and join the team.
                      </p>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-1">
                      <a class="Nav-dropdown-link" href="#volunteer-with-us">Volunteer With Us</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-2">
                      <a class="Nav-dropdown-link" href="#become-a-partner">Become a Partner</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-3">
                      <a class="Nav-dropdown-link" href="#build-your-skillset">Build Your Skillset</a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            
            <li class="Nav-item">
              <button class="Nav-toggle Nav-link" data-expands="nav-398" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">
                Ways to Give
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">
                  <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>
                </svg>
              </button>
              
              <div class="Nav-dropdown" id="nav-398" aria-hidden="true">
                <div class="Nav-dropdown-wrap">
                  <ul class="Nav-dropdown-list" role="list">
                    <li class="Nav-dropdown-parent">
                      <a class="Nav-dropdown-title" href="#ways-to-give">
                        Ways to Give
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                          <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                          <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                        </svg>
                      </a>
                      <p class="Nav-dropdown-description">
                        Your donations help people on their worst days and allows us to launch responses when disaster strikes.
                      </p>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-1">
                      <a class="Nav-dropdown-link" href="#donate-today">Donate Today</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-2">
                      <a class="Nav-dropdown-link" href="#donate-monthly">Donate Monthly</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-3">
                      <a class="Nav-dropdown-link" href="#fundraise">Fundraise</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-4">
                      <a class="Nav-dropdown-link" href="#make-a-planned-gift">Make a Planned Gift</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-5">
                      <a class="Nav-dropdown-link" href="#your-donor-portal">Your Donor Portal</a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            
            <li class="Nav-item">
              <button class="Nav-toggle Nav-link" data-expands="nav-403" data-nav-item-toggle="" type="button" aria-haspopup="true" aria-expanded="false">
                About Grey Bull
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 11 7" preserveAspectRatio="xMidYMid meet" focusable="false" aria-hidden="true" width="11" height="7" class="icon">
                  <path d="M10.5 1.45L5.55 6.4.6 1.45 2.01.04l3.54 3.53L9.09.04z" fill="var(--icon-color, #000)"></path>
                </svg>
              </button>
              
              <div class="Nav-dropdown" id="nav-403" aria-hidden="true">
                <div class="Nav-dropdown-wrap">
                  <ul class="Nav-dropdown-list" role="list">
                    <li class="Nav-dropdown-parent">
                      <a class="Nav-dropdown-title" href="#about-us">
                        About Grey Bull
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" focusable="false" aria-hidden="true" width="24" height="24" class="icon">
                          <circle cx="12" cy="12" r="11" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></circle>
                          <path d="m10.213 7.15 5.215 5.215-5.215 5.215" stroke="var(--icon-color, #D0D3D4)" stroke-width="2"></path>
                        </svg>
                      </a>
                      <p class="Nav-dropdown-description">
                        Our unwavering commitment is to build resiliency for vulnerable communities across the globe.
                      </p>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-1">
                      <a class="Nav-dropdown-link" href="#leadership-and-board">Leadership &amp; Board</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-2">
                      <a class="Nav-dropdown-link" href="#partners">Partners</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-3">
                      <a class="Nav-dropdown-link" href="#financials-annual-reports">Financials &amp; Annual Reports</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-4">
                      <a class="Nav-dropdown-link" href="#press-center">Press Center</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-5">
                      <a class="Nav-dropdown-link" href="#government-relations">Government Relations</a>
                    </li>
                    <li class="Nav-dropdown-item animate-nav-dropdown-6">
                      <a class="Nav-dropdown-link" href="#careers">Careers</a>
                    </li>
                  </ul>
                </div>
              </div>
            </li>
            
            <li class="Nav-item is-mobile">
              <a class="Nav-link Nav-link--util" href="#store">
                Store
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13.125" viewBox="0 0 16 14" fill="none" focusable="false" aria-hidden="true" class="icon">
                  <path d="M4.03613 3.10352H14.194C14.7988 3.10352 15.2651 3.6362 15.1852 4.23568L14.529 9.15709H4.4397L4.03613 3.10352Z" fill="#38444A"></path>
                  <path d="M0 0.683594H4.64108L4.73019 1.89431H0V0.683594Z" fill="#38444A"></path>
                  <circle cx="4.43851" cy="12.3858" r="1.61429" fill="#38444A"></circle>
                  <circle cx="13.7207" cy="12.3858" r="1.61429" fill="#38444A"></circle>
                  <path d="M2.82422 1.08789H4.64029L5.64922 9.96647H3.83315L2.82422 1.08789Z" fill="#38444A"></path>
                  <path d="M3.83099 9.9668H14.1259V11.5811H4.03658L3.83099 9.9668Z" fill="#38444A"></path>
                </svg>
              </a>
            </li>
            <li class="Nav-item is-mobile">
              <a class="Nav-link Nav-link--util" href="#news">
                News &amp; Stories
              </a>
            </li>
            <li class="Nav-item is-mobile">
              <a class="Nav-link Nav-link--util" href="#request-help">
                Request Help
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="18" viewBox="0 0 15 18" fill="none" focusable="false" aria-hidden="true" class="icon">
                  <path d="M6.4375 1H13C13.5523 1 14 1.44772 14 2V16C14 16.5523 13.5523 17 13 17H6.4375M1 9H9.25M9.25 9L5.125 4.63636M9.25 9L5.125 13.3636" stroke="#BE2437" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
              </a>
            </li>
          </ul>
        </div>
      </nav>
    </div>
    
  </div><!-- end Header-wrap -->
</header>
