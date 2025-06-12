/**
 * WDM Custom Header JavaScript
 * Handles mega menu interactions and mobile responsiveness
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
        const header = document.querySelector('.wdm-header');
        if (!header) return;
        
        initializeMegaMenu(header);
        initializeSearch(header);
        initializeMobileMenu(header);
        initializeScrollBehavior(header);
        initializeOutsideClick(header);
    }
    
    /**
     * Initialize mega menu functionality
     */
    function initializeMegaMenu(header) {
        const megaMenuItems = header.querySelectorAll('.wdm-mega-menu-item');
        
        megaMenuItems.forEach(function(item) {
            const link = item.querySelector('a');
            const panel = item.querySelector('.wdm-mega-panel');
            
            if (link && panel) {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    toggleMegaPanel(item, panel, megaMenuItems);
                });
            }
        });
    }
    
    /**
     * Toggle mega menu panel
     */
    function toggleMegaPanel(activeItem, activePanel, allItems) {
        const isActive = activeItem.classList.contains('active');
        
        // Close all panels
        allItems.forEach(function(item) {
            item.classList.remove('active');
            const panel = item.querySelector('.wdm-mega-panel');
            if (panel) {
                panel.classList.remove('active');
            }
        });
        
        // Open clicked panel if it wasn't already active
        if (!isActive) {
            activeItem.classList.add('active');
            activePanel.classList.add('active');
        }
    }
    
    /**
     * Initialize search functionality
     */
    function initializeSearch(header) {
        const searchToggle = header.querySelector('.wdm-search-toggle');
        const searchInput = header.querySelector('.wdm-search-input');
        
        if (searchToggle && searchInput) {
            searchToggle.addEventListener('click', function(e) {
                e.preventDefault();
                toggleSearch(searchInput);
            });
            
            // Close search on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && searchInput.classList.contains('active')) {
                    closeSearch(searchInput);
                }
            });
        }
    }
    
    /**
     * Toggle search input visibility
     */
    function toggleSearch(searchInput) {
        if (searchInput.classList.contains('active')) {
            closeSearch(searchInput);
        } else {
            openSearch(searchInput);
        }
    }
    
    /**
     * Open search input
     */
    function openSearch(searchInput) {
        searchInput.classList.add('active');
        // Focus on input after animation
        setTimeout(function() {
            searchInput.focus();
        }, 100);
    }
    
    /**
     * Close search input
     */
    function closeSearch(searchInput) {
        searchInput.classList.remove('active');
        searchInput.blur();
    }
    
    /**
     * Initialize mobile menu functionality
     */
    function initializeMobileMenu(header) {
        const mobileToggle = header.querySelector('.wdm-mobile-toggle');
        const nav = header.querySelector('.wdm-nav');
        
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
        toggle.classList.toggle('active');
        nav.classList.toggle('active');
        
        // Close all mega panels when closing mobile menu
        if (!nav.classList.contains('active')) {
            const megaMenuItems = nav.querySelectorAll('.wdm-mega-menu-item');
            megaMenuItems.forEach(function(item) {
                item.classList.remove('active');
                const panel = item.querySelector('.wdm-mega-panel');
                if (panel) {
                    panel.classList.remove('active');
                }
            });
        }
    }
    
    /**
     * Initialize outside click functionality
     */
    function initializeOutsideClick(header) {
        document.addEventListener('click', function(e) {
            // Check if click is outside header
            if (!header.contains(e.target)) {
                closeAllPanels(header);
                closeSearch(header.querySelector('.wdm-search-input'));
            }
        });
    }
    
    /**
     * Close all mega menu panels
     */
    function closeAllPanels(header) {
        const megaMenuItems = header.querySelectorAll('.wdm-mega-menu-item');
        megaMenuItems.forEach(function(item) {
            item.classList.remove('active');
            const panel = item.querySelector('.wdm-mega-panel');
            if (panel) {
                panel.classList.remove('active');
            }
        });
    }
    
    /**
     * Initialize scroll behavior
     */
    function initializeScrollBehavior(header) {
        const secondaryHeader = header.querySelector('.wdm-secondary-header');
        const scrollToggle = header.querySelector('.wdm-scroll-toggle');
        const nav = header.querySelector('.wdm-nav');
        
        if (!secondaryHeader || !scrollToggle) return;
        
        let lastScrollTop = 0;
        let isScrolled = false;
        
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100 && !isScrolled) {
                // Hide secondary header and show scroll toggle
                secondaryHeader.classList.add('hidden');
                scrollToggle.style.display = 'flex';
                isScrolled = true;
            } else if (scrollTop <= 100 && isScrolled) {
                // Show secondary header and hide scroll toggle
                secondaryHeader.classList.remove('hidden');
                scrollToggle.style.display = 'none';
                isScrolled = false;
                
                // Reset scroll toggle and nav states
                scrollToggle.classList.remove('active');
                if (nav) nav.classList.remove('active');
            }
            
            lastScrollTop = scrollTop;
        });
        
        // Handle scroll toggle click
        scrollToggle.addEventListener('click', function() {
            scrollToggle.classList.toggle('active');
            if (nav) {
                nav.classList.toggle('active');
                
                // Close all mega panels when closing nav
                if (!nav.classList.contains('active')) {
                    closeAllPanels(header);
                }
            }
        });
    }

    /**
     * Handle window resize
     */
    window.addEventListener('resize', function() {
        const header = document.querySelector('.wdm-header');
        if (!header) return;
        
        // Close mobile menu on desktop
        if (window.innerWidth > 768) {
            const mobileToggle = header.querySelector('.wdm-mobile-toggle');
            const nav = header.querySelector('.wdm-nav');
            const scrollToggle = header.querySelector('.wdm-scroll-toggle');
            
            if (mobileToggle && nav) {
                mobileToggle.classList.remove('active');
                nav.classList.remove('active');
            }
            
            if (scrollToggle) {
                scrollToggle.classList.remove('active');
            }
        }
    });
    
})();
