// Base JavaScript entrypoint for Gitr Social Network

(function() {
    'use strict';

    // Initialize application when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeApp();
    });

    function initializeApp() {
        // Initialize theme
        initTheme();
        
        // Initialize forms
        initForms();
        
        // Initialize navigation
        initNavigation();
        
        // Initialize language switcher
        initLanguageSwitcher();
    }

    /**
     * Initialize theme handling
     */
    function initTheme() {
        const themeToggle = document.getElementById('theme-toggle');
        if (themeToggle) {
            themeToggle.addEventListener('click', toggleTheme);
        }

        // Apply saved theme
        const savedTheme = localStorage.getItem('theme') || 
                          (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
        applyTheme(savedTheme);
    }

    /**
     * Toggle between light and dark theme
     */
    function toggleTheme() {
        const currentTheme = document.body.classList.contains('dark-mode') ? 'dark' : 'light';
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
        
        applyTheme(newTheme);
        localStorage.setItem('theme', newTheme);
    }

    /**
     * Apply theme to document
     */
    function applyTheme(theme) {
        if (theme === 'dark') {
            document.body.classList.add('dark-mode');
        } else {
            document.body.classList.remove('dark-mode');
        }
    }

    /**
     * Initialize form handling
     */
    function initForms() {
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(function(alert) {
                alert.style.transition = 'opacity 0.3s ease';
                alert.style.opacity = '0';
                setTimeout(function() {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            });
        }, 5000);

        // Confirm deletions
        document.querySelectorAll('[data-confirm]').forEach(function(element) {
            element.addEventListener('click', function(e) {
                const message = element.getAttribute('data-confirm') || 'Are you sure?';
                if (!confirm(message)) {
                    e.preventDefault();
                }
            });
        });

        // Loading states for forms
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function() {
                const submitBtn = form.querySelector('button[type="submit"]');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner"></span>' + submitBtn.textContent;
                }
            });
        });
    }

    /**
     * Initialize navigation handling
     */
    function initNavigation() {
        // Mobile menu toggle
        const mobileMenuBtn = document.getElementById('mobile-menu-btn');
        const navMenu = document.getElementById('nav-menu');
        
        if (mobileMenuBtn && navMenu) {
            mobileMenuBtn.addEventListener('click', function() {
                navMenu.classList.toggle('show');
            });
        }

        // Active navigation link
        const currentPath = window.location.pathname;
        document.querySelectorAll('.nav-link').forEach(function(link) {
            if (link.getAttribute('href') === currentPath) {
                link.classList.add('active');
            }
        });
    }

    /**
     * Initialize language switcher
     */
    function initLanguageSwitcher() {
        document.querySelectorAll('.lang-btn').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                const language = btn.getAttribute('data-lang') || 
                               btn.href.split('lang=')[1];
                
                if (!language) return;

                // Update active state
                document.querySelectorAll('.lang-btn').forEach(function(b) {
                    b.classList.remove('active');
                });
                btn.classList.add('active');

                // Show loading state
                btn.innerHTML = '<span class="spinner"></span>';

                // Change language via API or direct navigation
                changeLanguage(language, function() {
                    btn.innerHTML = getLanguageFlag(language);
                });
            });
        });
    }

    /**
     * Change language
     */
    function changeLanguage(language, callback) {
        fetch('/_api/language.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'set',
                language: language
            })
        })
        .then(function(response) {
            if (response.ok) {
                window.location.reload();
            } else {
                // Fallback to direct navigation
                window.location.href = '?lang=' + language;
            }
        })
        .catch(function(error) {
            console.error('Language change failed:', error);
            // Fallback to direct navigation
            window.location.href = '?lang=' + language;
        })
        .finally(function() {
            if (callback) callback();
        });
    }

    /**
     * Get language flag emoji
     */
    function getLanguageFlag(language) {
        const flags = {
            'ru': '🇷🇺',
            'en': '🇬🇧'
        };
        return flags[language] || '';
    }

    /**
     * Utility functions
     */
    window.Gitr = {
        showAlert: function(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-' + (type || 'info');
            alertDiv.textContent = message;
            
            const container = document.querySelector('.main-container') || document.body;
            container.insertBefore(alertDiv, container.firstChild);
            
            // Auto-hide after 5 seconds
            setTimeout(function() {
                alertDiv.style.opacity = '0';
                setTimeout(function() {
                    if (alertDiv.parentNode) {
                        alertDiv.parentNode.removeChild(alertDiv);
                    }
                }, 300);
            }, 5000);
        },

        formatTime: function(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffInSeconds = Math.floor((now - date) / 1000);
            
            if (diffInSeconds < 60) {
                return 'just now';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return minutes + ' minute' + (minutes > 1 ? 's' : '') + ' ago';
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return hours + ' hour' + (hours > 1 ? 's' : '') + ' ago';
            } else {
                return date.toLocaleDateString();
            }
        },

        toggleFullscreen: function(element) {
            if (!document.fullscreenElement) {
                element.requestFullscreen().catch(function(err) {
                    console.error('Error attempting to enable fullscreen:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }
    };

    // Auto-resize textareas
    document.addEventListener('input', function(e) {
        if (e.target.tagName === 'TEXTAREA') {
            e.target.style.height = 'auto';
            e.target.style.height = e.target.scrollHeight + 'px';
        }
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        // Refresh page state if needed
        window.location.reload();
    });

})();
