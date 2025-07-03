/**
 * WDM Custom Header JavaScript - Grey Bull Style
 * Handles navigation interactions and mobile responsiveness
 */

(function () {
  "use strict";

  // Wait for DOM to be ready
  document.addEventListener("DOMContentLoaded", function () {
    initializeHeader();
  });

  /**
   * Initialize header functionality
   */
  function initializeHeader() {
    const header = document.querySelector(".wdm-main-header");
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
    const navItems = header.querySelectorAll(".Nav-item");

    navItems.forEach(function (item) {
      const toggle = item.querySelector(".Nav-toggle");
      const dropdown = item.querySelector(".Nav-dropdown, .Nav-megaDropdown");

      if (toggle && dropdown) {
        toggle.addEventListener("click", function (e) {
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
    const isActive = activeItem.classList.contains("active");

    // Close all dropdowns
    allItems.forEach(function (item) {
      item.classList.remove("active");
      const dropdown = item.querySelector(".Nav-dropdown, .Nav-megaDropdown");
      if (dropdown) {
        dropdown.classList.remove("active");
      }
    });

    // Open clicked dropdown if it wasn't already active
    if (!isActive) {
      activeItem.classList.add("active");
      activeDropdown.classList.add("active");
    }
  }

  /**
   * Initialize mobile menu functionality
   */
  function initializeMobileMenu(header) {
    const mobileToggle = header.querySelector(".wdm-hamburger-btn");
    const nav = header.querySelector(".Nav-expandable-wrap");
    const overlay = document.getElementById("wdm-mobile-menu");

    if (mobileToggle) {
      mobileToggle.addEventListener("click", function () {
        toggleMobileMenu(mobileToggle, nav, overlay, header);
      });
    }

    if (overlay) {
      overlay.querySelectorAll(".mobile-menu-toggle").forEach(function (btn) {
        const id = btn.getAttribute("data-expands");
        const panel = overlay.querySelector("#" + id);
        btn.addEventListener("click", function () {
          const expanded = btn.getAttribute("aria-expanded") === "true";
          btn.setAttribute("aria-expanded", !expanded);
          if (panel) {
            panel.classList.toggle("active");
          }
        });
      });
    }
  }

  /**
   * Toggle mobile menu
   */
  function toggleMobileMenu(toggle, nav, overlay, header) {
    const isMobile = window.innerWidth <= 768 && overlay;

    if (isMobile) {
      const open = overlay.classList.contains("active");
      if (open) {
        overlay.classList.remove("active");
        toggle.classList.remove("active");
        header.classList.remove("nav-open");
      } else {
        overlay.classList.add("active");
        toggle.classList.add("active");
        header.classList.add("nav-open");
      }
      return;
    }

    const isActive = nav && nav.classList.contains("active");

    if (nav) {
      if (isActive) {
        nav.classList.remove("active");
        toggle.classList.remove("active");
        if (header) {
          header.classList.remove("nav-open");
        }
      } else {
        nav.classList.add("active");
        toggle.classList.add("active");
        if (header) {
          header.classList.add("nav-open");
        }
      }
    }
  }

  /**
   * Initialize outside click functionality
   */
  function initializeOutsideClick(header) {
    const overlay = document.getElementById("wdm-mobile-menu");
    document.addEventListener("click", function (e) {
      const clickInsideHeader = header.contains(e.target);
      const clickInsideOverlay = overlay && overlay.contains(e.target);

      if (!clickInsideHeader && !clickInsideOverlay) {
        closeAllDropdowns(header);
        if (overlay && overlay.classList.contains("active")) {
          overlay.classList.remove("active");
          const toggle = header.querySelector(".wdm-hamburger-btn");
          if (toggle) toggle.classList.remove("active");
          header.classList.remove("nav-open");
        }
      }
    });
  }

  /**
   * Close all dropdown panels
   */
  function closeAllDropdowns(header) {
    const navItems = header.querySelectorAll(".Nav-item");
    navItems.forEach(function (item) {
      item.classList.remove("active");
      const dropdown = item.querySelector(".Nav-dropdown, .Nav-megaDropdown");
      if (dropdown) {
        dropdown.classList.remove("active");
      }
    });
  }

  /**
   * Handle window resize
   */
  window.addEventListener("resize", function () {
    const header = document.querySelector(".wdm-main-header");
    if (!header) return;

    // Close mobile menu on desktop
    if (window.innerWidth > 768) {
      const mobileToggle = header.querySelector(".wdm-hamburger-btn");
      const nav = header.querySelector(".Nav-expandable-wrap");

      if (mobileToggle && nav) {
        mobileToggle.classList.remove("active");
        nav.classList.remove("active");
      }

      // Close all dropdowns
      closeAllDropdowns(header);
    }
  });

  /**
   * Handle escape key
   */
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      const header = document.querySelector(".wdm-main-header");
      if (header) {
        closeAllDropdowns(header);

        // Close mobile menu
        const mobileToggle = header.querySelector(".wdm-hamburger-btn");
        const nav = header.querySelector(".Nav-expandable-wrap");
        const overlay = document.getElementById("wdm-mobile-menu");

        if (overlay && overlay.classList.contains("active")) {
          overlay.classList.remove("active");
          if (mobileToggle) mobileToggle.classList.remove("active");
          header.classList.remove("nav-open");
        } else if (mobileToggle && nav && nav.classList.contains("active")) {
          mobileToggle.classList.remove("active");
          nav.classList.remove("active");
          header.classList.remove("nav-open");
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
    const scrollThreshold =
      parseInt(header.getAttribute("data-scroll-trigger")) || 400;
    const hysteresis = parseInt(header.getAttribute("data-hysteresis")) || 10;
    const alertBanner = document.querySelector(".emergency-alert-banner");

    function updateScrollState() {
      const scrollTop =
        window.pageYOffset || document.documentElement.scrollTop;
      let shouldBeScrolled;

      // Emergency Alert Toggle (show only when near top of page)
      if (alertBanner) {
        if (scrollTop <= 50) {
          alertBanner.classList.remove("collapsed");
          alertBanner.setAttribute("aria-hidden", "false");
        } else {
          alertBanner.classList.add("collapsed");
          alertBanner.setAttribute("aria-hidden", "true");
        }
      }

      // Use different thresholds for scrolling down vs up to prevent flickering
      if (isScrolled) {
        // When already scrolled, need to go below threshold minus hysteresis to unscroll
        shouldBeScrolled = scrollTop > scrollThreshold - hysteresis;
      } else {
        // When not scrolled, need to go above threshold plus hysteresis to scroll
        shouldBeScrolled = scrollTop > scrollThreshold + hysteresis;
      }

      // Only update if state actually changed
      if (shouldBeScrolled !== isScrolled) {
        isScrolled = shouldBeScrolled;

        if (isScrolled) {
          header.classList.add("scrolled");
          // Use the reset function to properly sync state
          if (header._resetHamburgerState) {
            header._resetHamburgerState();
          }
        } else {
          header.classList.remove("scrolled");
          // Use the reset function to properly sync state
          if (header._resetHamburgerState) {
            header._resetHamburgerState();
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

    window.addEventListener("scroll", requestTick, { passive: true });
  }

  /**
   * Initialize hamburger menu
   */
  function initializeHamburgerMenu(header) {
    const hamburger = header.querySelector(".wdm-hamburger-btn");
    let isProcessing = false;
    let menuIsOpen = false; // Track state independently

    // Store reference to reset function for scroll behavior
    header._resetHamburgerState = function () {
      menuIsOpen = false;
      header.classList.remove("nav-open");
      if (hamburger) {
        hamburger.classList.remove("active");
      }
    };

    if (hamburger) {
      // Ensure menu starts in closed state
      header._resetHamburgerState();

      hamburger.addEventListener("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Prevent rapid clicks during animation
        if (isProcessing) return;
        isProcessing = true;

        console.log("Hamburger clicked"); // Debug log
        console.log("Menu state variable:", menuIsOpen); // Debug log

        if (menuIsOpen) {
          header.classList.remove("nav-open");
          hamburger.classList.remove("active");
          menuIsOpen = false;
          console.log("Menu closed"); // Debug log
        } else {
          header.classList.add("nav-open");
          hamburger.classList.add("active");
          menuIsOpen = true;
          console.log("Menu opened"); // Debug log
        }

        // Reset processing flag after animation
        setTimeout(function () {
          isProcessing = false;
        }, 350);
      });
    }
  }
})();
