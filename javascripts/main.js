
class FormValidator {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        if (this.form) {
            this.form.addEventListener('submit', (e) => this.validate(e));
        }
    }

    validate(e) {
        const inputs = this.form.querySelectorAll('input[required], textarea[required], select[required]');
        let isValid = true;

        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
                this.showError(input, 'This field is required');
            } else {
                this.clearError(input);
            }
        });

        if (!isValid) {
            e.preventDefault();
        }

        return isValid;
    }

    validateField(field) {
        const value = field.value.trim();

        if (!value) {
            return false;
        }

        if (field.type === 'email') {
            return this.validateEmail(value);
        }

        if (field.name === 'password') {
            return value.length >= 6;
        }

        if (field.name === 'username') {
            return value.length >= 3;
        }

        if (field.name === 'full_name') {
            return value.length >= 2;
        }

        return true;
    }

    validateEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    showError(field, message) {
        field.style.borderColor = '#dc3545';
        let errorDiv = field.nextElementSibling;
        
        if (!errorDiv || !errorDiv.classList.contains('error-text')) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'error-text';
            field.parentNode.insertBefore(errorDiv, field.nextSibling);
        }
        
        errorDiv.textContent = message;
        errorDiv.style.color = '#f8d7da';
        errorDiv.style.fontSize = '12px';
        errorDiv.style.marginTop = '5px';
    }

    clearError(field) {
        field.style.borderColor = '';
        const errorDiv = field.nextElementSibling;
        
        if (errorDiv && errorDiv.classList.contains('error-text')) {
            errorDiv.remove();
        }
    }
}

class Modal {
    constructor(modalId) {
        this.modal = document.getElementById(modalId);
        this.setupEventListeners();
    }

    setupEventListeners() {
        if (!this.modal) return;

        const closeBtn = this.modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => this.close());
        }

        this.modal.addEventListener('click', (e) => {
            if (e.target === this.modal) {
                this.close();
            }
        });
    }

    open() {
        if (this.modal) {
            this.modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        }
    }

    close() {
        if (this.modal) {
            this.modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
}

class SearchHandler {
    constructor(formSelector, inputSelector) {
        this.form = document.querySelector(formSelector);
        this.input = document.querySelector(inputSelector);
        
        if (this.form && this.input) {
            this.setupEventListeners();
        }
    }

    setupEventListeners() {
        this.form.addEventListener('submit', (e) => {
            const query = this.input.value.trim();
            if (!query) {
                e.preventDefault();
                alert('Please enter a search term');
            }
        });

        this.input.addEventListener('input', (e) => {
            this.handleInput(e.target.value);
        });
    }

    handleInput(value) {
        if (value.length < 2) {
            this.clearSuggestions();
            return;
        }

    }

    clearSuggestions() {
        const suggestions = document.querySelector('.search-suggestions');
        if (suggestions) {
            suggestions.remove();
        }
    }
}

// ============================================
// NOTIFICATION SYSTEM
// ============================================

class Notification {
    static show(message, type = 'info', duration = 3000) {
        const notification = document.createElement('div');
        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            background: ${this.getColor(type)};
            color: white;
            border-radius: 5px;
            z-index: 10000;
            animation: slideIn 0.3s ease-in-out;
            max-width: 400px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in-out';
            setTimeout(() => notification.remove(), 300);
        }, duration);
    }

    static success(message, duration = 3000) {
        this.show(message, 'success', duration);
    }

    static error(message, duration = 5000) {
        this.show(message, 'danger', duration);
    }

    static info(message, duration = 3000) {
        this.show(message, 'info', duration);
    }

    static warning(message, duration = 4000) {
        this.show(message, 'warning', duration);
    }

    static getColor(type) {
        const colors = {
            success: '#28a745',
            danger: '#dc3545',
            warning: '#ffc107',
            info: '#1167cf'
        };
        return colors[type] || colors.info;
    }
}

// ============================================
// CONFIRMATION DIALOG
// ============================================

class Confirm {
    static show(message, onConfirm, onCancel = null) {
        const modal = document.createElement('div');
        modal.className = 'confirm-modal';
        modal.innerHTML = `
            <div class="confirm-content">
                <h3>Confirm</h3>
                <p>${message}</p>
                <div class="confirm-buttons">
                    <button class="btn btn-danger" id="confirm-yes">Yes, Confirm</button>
                    <button class="btn" id="confirm-no">Cancel</button>
                </div>
            </div>
        `;

        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        `;

        const content = modal.querySelector('.confirm-content');
        content.style.cssText = `
            background: #1a1a1a;
            padding: 30px;
            border-radius: 8px;
            max-width: 400px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        `;

        const buttons = modal.querySelector('.confirm-buttons');
        buttons.style.cssText = `
            display: flex;
            gap: 10px;
            margin-top: 20px;
        `;

        document.body.appendChild(modal);

        modal.querySelector('#confirm-yes').addEventListener('click', () => {
            modal.remove();
            if (onConfirm) onConfirm();
        });

        modal.querySelector('#confirm-no').addEventListener('click', () => {
            modal.remove();
            if (onCancel) onCancel();
        });
    }
}

// ============================================
// RATING COMPONENT
// ============================================

class RatingWidget {
    constructor(containerId) {
        this.container = document.getElementById(containerId);
        if (this.container) {
            this.setupRating();
        }
    }

    setupRating() {
        const inputId = this.container.getAttribute('data-input-id');
        const input = document.getElementById(inputId);
        
        if (!input) return;

        for (let i = 1; i <= 10; i++) {
            const star = document.createElement('span');
            star.className = 'rating-star';
            star.textContent = '★';
            star.style.cssText = `
                cursor: pointer;
                font-size: 24px;
                color: #999;
                transition: color 0.2s;
                margin: 0 5px;
            `;

            star.addEventListener('mouseenter', () => {
                this.highlightStars(i);
            });

            star.addEventListener('click', () => {
                input.value = i;
                this.highlightStars(i);
            });

            this.container.appendChild(star);
        }

        this.container.addEventListener('mouseleave', () => {
            const currentValue = input.value || 0;
            this.highlightStars(currentValue);
        });
    }

    highlightStars(rating) {
        const stars = this.container.querySelectorAll('.rating-star');
        stars.forEach((star, index) => {
            if (index < rating) {
                star.style.color = '#ffc107';
            } else {
                star.style.color = '#999';
            }
        });
    }
}

// ============================================
// TABLE SORTING
// ============================================

class TableSort {
    constructor(tableSelector) {
        this.table = document.querySelector(tableSelector);
        if (this.table) {
            this.setupSorting();
        }
    }

    setupSorting() {
        const headers = this.table.querySelectorAll('th');
        headers.forEach((header, index) => {
            header.style.cursor = 'pointer';
            header.addEventListener('click', () => {
                this.sortColumn(index);
            });
        });
    }

    sortColumn(columnIndex) {
        const tbody = this.table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        rows.sort((a, b) => {
            const aVal = a.cells[columnIndex].textContent.trim();
            const bVal = b.cells[columnIndex].textContent.trim();

            if (!isNaN(aVal) && !isNaN(bVal)) {
                return parseFloat(aVal) - parseFloat(bVal);
            }

            return aVal.localeCompare(bVal);
        });

        rows.forEach(row => tbody.appendChild(row));
    }
}

// ============================================
// PAGINATION
// ============================================

class Pagination {
    constructor(containerSelector, itemsPerPage = 10) {
        this.container = document.querySelector(containerSelector);
        this.itemsPerPage = itemsPerPage;
        if (this.container) {
            this.setupPagination();
        }
    }

    setupPagination() {
        const items = this.container.querySelectorAll('[data-page-item]');
        const totalPages = Math.ceil(items.length / this.itemsPerPage);

        this.showPage(1, items);
        this.renderPageButtons(totalPages, items);
    }

    showPage(pageNum, items) {
        const start = (pageNum - 1) * this.itemsPerPage;
        const end = start + this.itemsPerPage;

        items.forEach((item, index) => {
            item.style.display = (index >= start && index < end) ? 'block' : 'none';
        });
    }

    renderPageButtons(totalPages, items) {
        const paginationDiv = document.querySelector('.pagination');
        if (!paginationDiv) return;

        paginationDiv.innerHTML = '';

        for (let i = 1; i <= totalPages; i++) {
            const button = document.createElement('a');
            button.textContent = i;
            button.href = '#';
            button.className = i === 1 ? 'active' : '';

            button.addEventListener('click', (e) => {
                e.preventDefault();
                document.querySelectorAll('.pagination a').forEach(b => b.classList.remove('active'));
                button.classList.add('active');
                this.showPage(i, items);
                window.scrollTo(0, 0);
            });

            paginationDiv.appendChild(button);
        }
    }
}

// ============================================
// LAZY LOADING
// ============================================

class LazyLoad {
    constructor() {
        if ('IntersectionObserver' in window) {
            this.setupLazyLoad();
        }
    }

    setupLazyLoad() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });

        document.querySelectorAll('img[data-src]').forEach(img => {
            observer.observe(img);
        });
    }
}

// ============================================
// UTILITIES
// ============================================

class Utils {
    // Format date
    static formatDate(date) {
        const options = { year: 'numeric', month: 'short', day: 'numeric' };
        return new Date(date).toLocaleDateString('en-US', options);
    }

    // Debounce function
    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Throttle function
    static throttle(func, limit) {
        let inThrottle;
        return function(...args) {
            if (!inThrottle) {
                func.apply(this, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Check if element is in viewport
    static isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Copy to clipboard
    static copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            Notification.success('Copied to clipboard!');
        }).catch(() => {
            Notification.error('Failed to copy');
        });
    }

    // Get URL parameter
    static getUrlParam(param) {
        const params = new URLSearchParams(window.location.search);
        return params.get(param);
    }
}

// ============================================
// INITIALIZATION
// ============================================

document.addEventListener('DOMContentLoaded', () => {
    // Initialize components if they exist
    if (document.querySelector('form')) {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            new FormValidator('form');
        });
    }

    // Initialize lazy loading
    new LazyLoad();

    // Add smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });

    // Confirm delete buttons
    document.querySelectorAll('[data-confirm]').forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm');
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });

    console.log('EM reviews initialized');
});

// ============================================
// EXPORT FOR MODULES
// ============================================

if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        FormValidator,
        Modal,
        SearchHandler,
        Notification,
        Confirm,
        RatingWidget,
        TableSort,
        Pagination,
        LazyLoad,
        Utils
    };
}
