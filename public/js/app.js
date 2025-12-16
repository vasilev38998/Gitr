// Gitr Social Network JavaScript

// Global state
let currentUser = null;

// Initialize app
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

async function initializeApp() {
    try {
        // Check if user is authenticated
        const response = await fetch('/api/auth.php');
        const authData = await response.json();
        
        if (authData.authenticated) {
            currentUser = authData.user;
        }
    } catch (error) {
        console.log('Not authenticated or API not available');
    }
}

// Authentication functions
async function login(email, password) {
    try {
        const response = await fetch('/api/auth.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email, password })
        });
        
        const result = await response.json();
        
        if (result.success) {
            window.location.href = '/feed.php';
        } else {
            alert('Login failed: ' + result.error);
        }
    } catch (error) {
        console.error('Login error:', error);
        alert('Login failed. Please try again.');
    }
}

async function logout() {
    try {
        await fetch('/api/auth.php', {
            method: 'DELETE'
        });
        
        window.location.href = '/';
    } catch (error) {
        console.error('Logout error:', error);
        // Force logout on frontend
        window.location.href = '/';
    }
}

// Post functions
async function createPost(content) {
    try {
        const response = await fetch('/api/posts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ content })
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result.post;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Create post error:', error);
        throw error;
    }
}

async function toggleLike(postId) {
    try {
        // Disable button temporarily to prevent double-clicks
        const button = event.target.closest('.like-btn');
        if (button.disabled) return;
        
        button.disabled = true;
        
        const response = await fetch('/api/like.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_id: postId })
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update button state
            const countSpan = button.querySelector('.count');
            const heartSpan = button.querySelector('.heart');
            
            countSpan.textContent = result.likes_count;
            
            if (result.action === 'liked') {
                button.classList.add('liked');
                heartSpan.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    heartSpan.style.transform = 'scale(1)';
                }, 150);
            } else if (result.action === 'unliked') {
                button.classList.remove('liked');
            }
            
            // Add small animation
            button.style.transform = 'scale(0.95)';
            setTimeout(() => {
                button.style.transform = 'scale(1)';
            }, 150);
            
        } else {
            alert('Error: ' + result.error);
        }
    } catch (error) {
        console.error('Toggle like error:', error);
        alert('Failed to like/unlike post');
    } finally {
        button.disabled = false;
    }
}

async function createComment(postId, content) {
    try {
        const response = await fetch('/api/comment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ post_id: postId, content })
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result.comment;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Create comment error:', error);
        throw error;
    }
}

// Feed functions
async function loadFeed(limit = 20, offset = 0) {
    try {
        const response = await fetch(`/api/feed.php?limit=${limit}&offset=${offset}`);
        const result = await response.json();
        
        if (result.success) {
            return result.posts;
        } else {
            throw new Error(result.error);
        }
    } catch (error) {
        console.error('Load feed error:', error);
        throw error;
    }
}

async function loadRecentPosts() {
    try {
        const postsList = document.getElementById('recent-posts-list');
        if (!postsList) return;
        
        const posts = await loadFeed(5, 0);
        
        if (posts.length === 0) {
            postsList.innerHTML = '<p>No posts yet. Be the first to post!</p>';
            return;
        }
        
        postsList.innerHTML = posts.map(post => `
            <div class="post-card">
                <div class="post-header">
                    <div class="post-author-avatar">
                        ${post.avatar ? 
                            `<img src="${escapeHtml(post.avatar)}" alt="Avatar">` : 
                            `<div class="avatar-placeholder">${escapeHtml(post.username.substring(0, 2).toUpperCase())}</div>`
                        }
                    </div>
                    <div class="post-author-info">
                        <span class="post-author">${escapeHtml(post.username)}</span>
                        <span class="post-date">${formatDate(post.created_at)}</span>
                    </div>
                </div>
                <div class="post-content">${escapeHtml(post.content)}</div>
                <div class="post-actions">
                    <button class="action-btn like-btn ${post.is_liked ? 'liked' : ''}" 
                            onclick="toggleLike(${post.id})">
                        <span class="heart">♥</span>
                        <span class="count">${post.likes_count}</span>
                    </button>
                    <a href="/post/view.php?id=${post.id}" class="action-btn comment-btn">
                        <span>💬</span>
                        <span class="count">${post.comments_count}</span>
                    </a>
                </div>
            </div>
        `).join('');
        
    } catch (error) {
        console.error('Load recent posts error:', error);
        const postsList = document.getElementById('recent-posts-list');
        if (postsList) {
            postsList.innerHTML = '<p>Failed to load posts. Please try again later.</p>';
        }
    }
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 1) {
        return 'Today';
    } else if (diffDays === 2) {
        return 'Yesterday';
    } else if (diffDays <= 7) {
        return `${diffDays - 1} days ago`;
    } else {
        return date.toLocaleDateString();
    }
}

function truncateText(text, maxLength = 150) {
    if (text.length <= maxLength) return text;
    return text.substring(0, maxLength) + '...';
}

// Auto-resize textarea
document.addEventListener('input', function(e) {
    if (e.target.tagName === 'TEXTAREA') {
        e.target.style.height = 'auto';
        e.target.style.height = e.target.scrollHeight + 'px';
    }
});

// Smooth scrolling for anchor links
document.addEventListener('click', function(e) {
    if (e.target.tagName === 'A' && e.target.getAttribute('href')?.startsWith('#')) {
        e.preventDefault();
        const targetId = e.target.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            targetElement.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }
});

// Loading states for buttons
function showLoading(button) {
    button.disabled = true;
    button.dataset.originalText = button.textContent;
    button.textContent = 'Loading...';
}

function hideLoading(button) {
    button.disabled = false;
    button.textContent = button.dataset.originalText;
}

// Image error handling
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        e.target.style.display = 'none';
        const placeholder = document.createElement('div');
        placeholder.className = 'avatar-placeholder';
        placeholder.textContent = e.target.alt?.substring(0, 2).toUpperCase() || '??';
        e.target.parentNode.appendChild(placeholder);
    }
}, true);

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + Enter to submit forms
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        const activeElement = document.activeElement;
        
        if (activeElement.tagName === 'TEXTAREA') {
            const form = activeElement.closest('form');
            if (form) {
                form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
            }
        }
    }
});

// Export functions for global use
window.Gitr = {
    login,
    logout,
    createPost,
    toggleLike,
    createComment,
    loadFeed,
    loadRecentPosts
};