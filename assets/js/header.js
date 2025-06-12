/**
 * WDM Custom Header JavaScript - Grey Bull Style
 * Handles navigation interactions and mobile responsiveness
 */

(function() {
    'use strict';
    
    // Wait for DOM to be ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeHeader();
    });
    
    /**
     * Initialize header functionality
     */
    function initializeHeader() {
        const header = document.querySelector('.wdm-main-header');
        if (!header) return;
        
        initializeNavigation(header);
        initializeMobileMenu(header);
        initializeOutsideClick(header);
        initializeScrollBehavior(header);
        initializeHamburgerMenu(header);
    }
    
    /**
     * Initialize navigation functionality
     */
    function initializeNavigation(header) {
        const navItems = header.querySelectorAll('.Nav-item');
        
        navItems.forEach(function(item) {
            const toggle = item.querySelector('.Nav-toggle');
            const dropdown = item.querySelector('.Nav-dropdown, .Nav-megaDropdown');
            
            if (toggle && dropdown) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleDropdown(item, dropdown, navItems);
                });
            }
        });
    }
    
    /**
     * Toggle dropdown panel
     */
    function toggleDropdown(activeItem, activeDropdown, allItems) {
        const isActive = activeItem.classList.contains('active');
        
        // Close all dropdowns
        allItems.forEach(function(item) {
            item.classList.remove('active');
            const dropdown = item.querySelector('.Nav-dropdown, .Nav-megaDropdown');
            if (dropdown) {
                dropdown.classList.remove('active');
            }
        });
        
        // Open clicked dropdown if it wasn't already active
        if (!isActive) {
            activeItem.classList.add('active');
            activeDropdown.classList.add('active');
        }
    }
    
    /**
     * Initialize mobile menu functionality
     */
    function initializeMobileMenu(header) {
        const mobileToggle = header.querySelector('.wdm-hamburger-btn');
        const nav = header.querySelector('.Nav-expandable-wrap');
        
        if (mobileToggle && nav) {
            mobileToggle.addEventListener('click', function() {
                toggleMobileMenu(mobileToggle, nav);
            });
        }
    }
    
    /**
     * Toggle mobile menu
     */
    function toggleMobileMenu(toggle, nav) {
        const isActive = nav.classList.contains('active');
        const header = toggle.closest('.wdm-main-header');
        
        if (isActive) {
            nav.classList.remove('active');
            toggle.classList.remove('active');
            
            // Close all dropdowns when closing mobile menu
            const navItems = nav.querySelectorAll('.Nav-item');
            navItems.forEach(function(item) {
                item.classList.remove('active');
                const dropdown = item.querySelector('.Nav-dropdown, .Nav-megaDropdown');
                if (dropdown) {
                    dropdown.classList.remove('active');
                }
            });
            
            // Remove nav-open class from header
            if (header) {
                header.classList.remove('nav-open');
            }
        } else {
            nav.classList.add('active');
            toggle.classList.add('active');
            
            // Add nav-open class to header
            if (header) {
                header.classList.add('nav-open');
            }
        }
    }
    
    /**
     * Initialize outside click functionality
     */
    function initializeOutsideClick(header) {
        document.addEventListener('click', function(e) {
            // Check if click is outside header
            if (!header.contains(e.target)) {
                closeAllDropdowns(header);
            }
        });
    }
    
    /**
     * Close all dropdown panels
     */
    function closeAllDropdowns(header) {
        const navItems = header.querySelectorAll('.Nav-item');
        navItems.forEach(function(item) {
            item.classList.remove('active');
            const dropdown = item.querySelector('.Nav-dropdown, .Nav-megaDropdown');
            if (dropdown) {
                dropdown.classList.remove('active');
            }
        });
    }
    
    /**
     * Handle window resize
     */
    window.addEventListener('resize', function() {
        const header = document.querySelector('.wdm-main-header');
        if (!header) return;
        
        // Close mobile menu on desktop
        if (window.innerWidth > 768) {
            const mobileToggle = header.querySelector('.wdm-hamburger-btn');
            const nav = header.querySelector('.Nav-expandable-wrap');
            
            if (mobileToggle && nav) {
                mobileToggle.classList.remove('active');
                nav.classList.remove('active');
            }
            
            // Close all dropdowns
            closeAllDropdowns(header);
        }
    });
    
    /**
     * Handle escape key
     */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const header = document.querySelector('.wdm-main-header');
            if (header) {
                closeAllDropdowns(header);
                
                // Close mobile menu
                const mobileToggle = header.querySelector('.wdm-hamburger-btn');
                const nav = header.querySelector('.Nav-expandable-wrap');
                
                if (mobileToggle && nav && nav.classList.contains('active')) {
                    mobileToggle.classList.remove('active');
                    nav.classList.remove('active');
                }
            }
        }
    });
    
    /**
     * Initialize scroll behavior
     */
    function initializeScrollBehavior(header) {
        let isScrolled = false;
        let ticking = false;
        const scrollThreshold = 100;
        const hysteresis = 10; // Add hysteresis to prevent flickering
        
        function updateScrollState() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            let shouldBeScrolled;
            
            // Use different thresholds for scrolling down vs up to prevent flickering
            if (isScrolled) {
                // When already scrolled, need to go below threshold minus hysteresis to unscroll
                shouldBeScrolled = scrollTop > (scrollThreshold - hysteresis);
            } else {
                // When not scrolled, need to go above threshold plus hysteresis to scroll
                shouldBeScrolled = scrollTop > (scrollThreshold + hysteresis);
            }
            
            // Only update if state actually changed
            if (shouldBeScrolled !== isScrolled) {
                isScrolled = shouldBeScrolled;
                
                if (isScrolled) {
                    header.classList.add('scrolled');
                    // Ensure nav starts closed when scrolled state is activated
                    header.classList.remove('nav-open');
                    const hamburger = header.querySelector('.wdm-hamburger-btn');
                    if (hamburger) {
                        hamburger.classList.remove('active');
                    }
                } else {
                    header.classList.remove('scrolled');
                    header.classList.remove('nav-open');
                    const hamburger = header.querySelector('.wdm-hamburger-btn');
                    if (hamburger) {
                        hamburger.classList.remove('active');
                    }
                }
            }
            
            ticking = false;
        }
        
        function requestTick() {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(updateScrollState);
            }
        }
        
        window.addEventListener('scroll', requestTick, { passive: true });
    }
    
    /**
     * Initialize hamburger menu
     */
    function initializeHamburgerMenu(header) {
        const hamburger = header.querySelector('.wdm-hamburger-btn');
        let isProcessing = false;
        
        if (hamburger) {
            // Ensure menu starts in closed state
            header.classList.remove('nav-open');
            hamburger.classList.remove('active');
            
            hamburger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                // Prevent rapid clicks during animation
                if (isProcessing) return;
                isProcessing = true;
                
                console.log('Hamburger clicked'); // Debug log
                
                const isOpen = header.classList.contains('nav-open');
                console.log('Menu is currently open:', isOpen); // Debug log
                
                if (isOpen) {
                    header.classList.remove('nav-open');
                    hamburger.classList.remove('active');
                    console.log('Menu closed'); // Debug log
                } else {
                    header.classList.add('nav-open');
                    hamburger.classList.add('active');
                    console.log('Menu opened'); // Debug log
                }
                
                // Reset processing flag after animation
                setTimeout(function() {
                    isProcessing = false;
                }, 350);
            });
        }
    }
    
})();