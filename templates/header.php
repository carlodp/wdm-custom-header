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

<header class="wdm-header">
    <!-- Top Navigation -->
    <div class="wdm-top-nav">
        <div class="wdm-container">
            <ul class="wdm-top-nav-links">
                <li><a href="#store">🛒 Store</a></li>
                <li><a href="#news">📰 News & Stories</a></li>
                <li><a href="#rollcall">📋 Roll Call</a></li>
                <li><a href="#cart">🛒 Cart</a></li>
                <li><a href="#login">👤 Login</a></li>
            </ul>
        </div>
    </div>

    <!-- Main Header -->
    <div class="wdm-main-header">
        <div class="wdm-container">
            <div class="wdm-header-content">
                <!-- Logo -->
                <div class="wdm-logo">
                    <?php if (!empty($atts['logo_url'])): ?>
                        <img src="<?php echo esc_url($atts['logo_url']); ?>" alt="<?php echo esc_attr($atts['logo_alt']); ?>">
                    <?php else: ?>
                        <svg width="120" height="50" viewBox="0 0 120 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect width="120" height="50" fill="#dc3545"/>
                            <text x="60" y="30" font-family="Arial, sans-serif" font-size="16" font-weight="bold" fill="white" text-anchor="middle">LOGO</text>
                        </svg>
                    <?php endif; ?>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="wdm-mobile-toggle" aria-label="Toggle Menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <!-- Navigation -->
                <nav class="wdm-nav">
                    <!-- Mega Menu -->
                    <ul class="wdm-mega-menu">
                        <li class="wdm-mega-menu-item">
                            <a href="#how-we-serve">How We Serve</a>
                            <div class="wdm-mega-panel">
                                <div class="wdm-mega-panel-content">
                                    <div>
                                        <h3>Disaster Response</h3>
                                        <ul>
                                            <li><a href="#emergency-response">Emergency Response</a></li>
                                            <li><a href="#relief-operations">Relief Operations</a></li>
                                            <li><a href="#recovery-support">Recovery Support</a></li>
                                            <li><a href="#preparedness">Preparedness</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Community Impact</h3>
                                        <ul>
                                            <li><a href="#community-projects">Community Projects</a></li>
                                            <li><a href="#veteran-services">Veteran Services</a></li>
                                            <li><a href="#youth-programs">Youth Programs</a></li>
                                            <li><a href="#partnerships">Partnerships</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Global Operations</h3>
                                        <p>Learn about our international disaster response and humanitarian aid efforts around the world.</p>
                                        <a href="#global-operations">View Global Impact →</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="wdm-mega-menu-item">
                            <a href="#get-involved">How to Get Involved</a>
                            <div class="wdm-mega-panel">
                                <div class="wdm-mega-panel-content">
                                    <div>
                                        <h3>Volunteer</h3>
                                        <ul>
                                            <li><a href="#volunteer-opportunities">Volunteer Opportunities</a></li>
                                            <li><a href="#training-programs">Training Programs</a></li>
                                            <li><a href="#team-leader">Become a Team Leader</a></li>
                                            <li><a href="#volunteer-resources">Volunteer Resources</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Corporate Partners</h3>
                                        <ul>
                                            <li><a href="#corporate-volunteering">Corporate Volunteering</a></li>
                                            <li><a href="#sponsorship">Sponsorship Opportunities</a></li>
                                            <li><a href="#employee-engagement">Employee Engagement</a></li>
                                            <li><a href="#partnership-benefits">Partnership Benefits</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Join Our Mission</h3>
                                        <p>Be part of a community dedicated to serving others in their time of greatest need.</p>
                                        <a href="#join-mission">Get Started Today →</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="wdm-mega-menu-item">
                            <a href="#ways-to-give">Ways to Give</a>
                            <div class="wdm-mega-panel">
                                <div class="wdm-mega-panel-content">
                                    <div>
                                        <h3>Make a Donation</h3>
                                        <ul>
                                            <li><a href="#one-time-donation">One-Time Donation</a></li>
                                            <li><a href="#monthly-giving">Monthly Giving</a></li>
                                            <li><a href="#disaster-relief">Disaster Relief Fund</a></li>
                                            <li><a href="#memorial-giving">Memorial Giving</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Other Ways to Give</h3>
                                        <ul>
                                            <li><a href="#workplace-giving">Workplace Giving</a></li>
                                            <li><a href="#planned-giving">Planned Giving</a></li>
                                            <li><a href="#fundraise">Fundraise for Us</a></li>
                                            <li><a href="#gift-cards">Gift Cards</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Impact Stories</h3>
                                        <p>See how your generous donations directly support our mission and help communities in need.</p>
                                        <a href="#impact-stories">Read Impact Stories →</a>
                                    </div>
                                </div>
                            </div>
                        </li>

                        <li class="wdm-mega-menu-item">
                            <a href="#about">About Team Rubicon</a>
                            <div class="wdm-mega-panel">
                                <div class="wdm-mega-panel-content">
                                    <div>
                                        <h3>Our Story</h3>
                                        <ul>
                                            <li><a href="#mission">Our Mission</a></li>
                                            <li><a href="#history">Our History</a></li>
                                            <li><a href="#leadership">Leadership Team</a></li>
                                            <li><a href="#board">Board of Directors</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>Impact & Results</h3>
                                        <ul>
                                            <li><a href="#annual-report">Annual Report</a></li>
                                            <li><a href="#financials">Financials</a></li>
                                            <li><a href="#impact-metrics">Impact Metrics</a></li>
                                            <li><a href="#testimonials">Testimonials</a></li>
                                        </ul>
                                    </div>
                                    <div>
                                        <h3>News & Media</h3>
                                        <p>Stay updated with the latest news, press releases, and media coverage of our work.</p>
                                        <a href="#news-media">View News & Media →</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>

                    <!-- Search -->
                    <div class="wdm-search">
                        <button class="wdm-search-toggle" aria-label="Search">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="11" cy="11" r="8"></circle>
                                <path d="m21 21-4.35-4.35"></path>
                            </svg>
                        </button>
                        <input type="search" class="wdm-search-input" placeholder="Search..." aria-label="Search">
                    </div>

                    <!-- Action Buttons -->
                    <div class="wdm-actions">
                        <a href="#volunteer" class="wdm-btn wdm-btn-volunteer">Volunteer</a>
                        <a href="#donate" class="wdm-btn wdm-btn-donate">Donate</a>
                    </div>
                </nav>
            </div>
        </div>
    </div>
</header>
