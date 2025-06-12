/**
 * WDM Custom Header JavaScript - Team Rubicon Style
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
        const header = document.querySelector('.Header');
        if (!header) return;
        
        initializeNavigation(header);
        initializeMobileMenu(header);
        initializeOutsideClick(header);
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
        const mobileToggle = header.querySelector('.Header-toggle');
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
        } else {
            nav.classList.add('active');
            toggle.classList.add('active');
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
        const header = document.querySelector('.Header');
        if (!header) return;
        
        // Close mobile menu on desktop
        if (window.innerWidth > 768) {
            const mobileToggle = header.querySelector('.Header-toggle');
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
            const header = document.querySelector('.Header');
            if (header) {
                closeAllDropdowns(header);
                
                // Close mobile menu
                const mobileToggle = header.querySelector('.Header-toggle');
                const nav = header.querySelector('.Nav-expandable-wrap');
                
                if (mobileToggle && nav && nav.classList.contains('active')) {
                    mobileToggle.classList.remove('active');
                    nav.classList.remove('active');
                }
            }
        }
    });
    
})();