<?php
/**
 * BuyBuy Modern Toast Notification System
 * Include this file in customer pages to get beautiful toast notifications
 * 
 * Usage (PHP):
 *   $_SESSION['notif'] = ['type' => 'success', 'message' => 'Done!'];
 *   $_SESSION['notif'] = ['type' => 'error',   'message' => 'Oops!'];
 *   $_SESSION['notif'] = ['type' => 'warning', 'message' => 'Careful!'];
 *   $_SESSION['notif'] = ['type' => 'info',    'message' => 'FYI...'];
 * 
 * Usage (JavaScript):
 *   showToast('success', 'Profile updated!');
 *   showToast('error',   'Something went wrong');
 */

// Grab notification from session (if any) and clear it
$__notif = null;
if(isset($_SESSION['notif'])){
    $__notif = $_SESSION['notif'];
    unset($_SESSION['notif']);
}

// Also support legacy ?cancel=success and ?refund=success URL params
if(isset($_GET['cancel']) && $_GET['cancel'] === 'success' && $__notif === null){
    $__notif = ['type' => 'success', 'message' => 'Cancellation request submitted successfully!'];
}
if(isset($_GET['refund']) && $_GET['refund'] === 'success' && $__notif === null){
    $__notif = ['type' => 'success', 'message' => 'Refund request submitted successfully!'];
}
?>

<!-- ============ TOAST NOTIFICATION STYLES ============ -->
<style>
/* Toast Container */
.toast-container{
    position:fixed;
    top:28px;
    right:28px;
    z-index:99999;
    display:flex;
    flex-direction:column;
    gap:12px;
    pointer-events:none;
}

/* Toast Card */
.toast-notif{
    pointer-events:auto;
    display:flex;
    align-items:center;
    gap:14px;
    min-width:340px;
    max-width:460px;
    padding:18px 22px;
    border-radius:18px;
    background:rgba(255,255,255,0.96);
    backdrop-filter:blur(18px) saturate(1.6);
    -webkit-backdrop-filter:blur(18px) saturate(1.6);
    box-shadow:
        0 20px 50px rgba(0,0,0,0.10),
        0 6px 16px rgba(0,0,0,0.06),
        inset 0 1px 0 rgba(255,255,255,0.8);
    border:1px solid rgba(255,255,255,0.6);
    transform:translateX(120%);
    opacity:0;
    animation:toastSlideIn 0.5s cubic-bezier(0.16,1,0.3,1) forwards;
    position:relative;
    overflow:hidden;
    cursor:pointer;
    transition:transform 0.2s ease, box-shadow 0.2s ease;
}

.toast-notif:hover{
    transform:translateY(-2px) !important;
    box-shadow:
        0 24px 56px rgba(0,0,0,0.12),
        0 8px 20px rgba(0,0,0,0.08),
        inset 0 1px 0 rgba(255,255,255,0.8);
}

/* Accent strip on the left */
.toast-notif::before{
    content:'';
    position:absolute;
    left:0;
    top:0;
    bottom:0;
    width:5px;
    border-radius:18px 0 0 18px;
}

/* Type-specific accent colors */
.toast-notif.toast-success::before{ background:linear-gradient(180deg, #34d399, #10b981); }
.toast-notif.toast-error::before{ background:linear-gradient(180deg, #f87171, #ef4444); }
.toast-notif.toast-warning::before{ background:linear-gradient(180deg, #fbbf24, #f59e0b); }
.toast-notif.toast-info::before{ background:linear-gradient(180deg, #60a5fa, #3b82f6); }

/* Icon circle */
.toast-icon{
    width:44px;
    height:44px;
    min-width:44px;
    border-radius:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
}

.toast-success .toast-icon{
    background:linear-gradient(135deg, #ecfdf5, #d1fae5);
    color:#059669;
}

.toast-error .toast-icon{
    background:linear-gradient(135deg, #fef2f2, #fecaca);
    color:#dc2626;
}

.toast-warning .toast-icon{
    background:linear-gradient(135deg, #fffbeb, #fef3c7);
    color:#d97706;
}

.toast-info .toast-icon{
    background:linear-gradient(135deg, #eff6ff, #dbeafe);
    color:#2563eb;
}

/* Text content */
.toast-content{
    flex:1;
    min-width:0;
}

.toast-title{
    font-weight:700;
    font-size:14px;
    color:#1f2937;
    margin-bottom:2px;
    letter-spacing:-0.01em;
}

.toast-message{
    font-size:13px;
    color:#6b7280;
    line-height:1.45;
    word-break:break-word;
}

/* Close button */
.toast-close{
    background:none;
    border:none;
    color:#9ca3af;
    font-size:18px;
    cursor:pointer;
    padding:4px;
    border-radius:8px;
    transition:0.2s ease;
    display:flex;
    align-items:center;
    justify-content:center;
    min-width:28px;
    height:28px;
}

.toast-close:hover{
    background:#f3f4f6;
    color:#374151;
}

/* Progress bar */
.toast-progress{
    position:absolute;
    bottom:0;
    left:0;
    height:3px;
    border-radius:0 0 18px 18px;
    animation:toastProgress 4s linear forwards;
}

.toast-success .toast-progress{ background:linear-gradient(90deg, #34d399, #10b981); }
.toast-error .toast-progress{ background:linear-gradient(90deg, #f87171, #ef4444); }
.toast-warning .toast-progress{ background:linear-gradient(90deg, #fbbf24, #f59e0b); }
.toast-info .toast-progress{ background:linear-gradient(90deg, #60a5fa, #3b82f6); }

/* Animations */
@keyframes toastSlideIn{
    0%{
        transform:translateX(120%);
        opacity:0;
    }
    100%{
        transform:translateX(0);
        opacity:1;
    }
}

@keyframes toastSlideOut{
    0%{
        transform:translateX(0);
        opacity:1;
    }
    100%{
        transform:translateX(120%);
        opacity:0;
    }
}

@keyframes toastProgress{
    from{ width:100%; }
    to{ width:0%; }
}

/* Responsive */
@media(max-width: 576px){
    .toast-container{
        top:16px;
        right:16px;
        left:16px;
    }
    .toast-notif{
        min-width:unset;
        max-width:100%;
    }
}
</style>

<!-- ============ TOAST CONTAINER ============ -->
<div class="toast-container" id="toastContainer"></div>

<!-- ============ TOAST JAVASCRIPT ============ -->
<script>
/**
 * Show a modern toast notification
 * @param {string} type - 'success' | 'error' | 'warning' | 'info'
 * @param {string} message - The notification message
 * @param {number} duration - Auto-dismiss time in ms (default 4000)
 */
function showToast(type, message, duration = 4000){

    const container = document.getElementById('toastContainer');

    const config = {
        success: { icon: 'bi-check-circle-fill', title: 'Success' },
        error:   { icon: 'bi-x-circle-fill',     title: 'Error' },
        warning: { icon: 'bi-exclamation-triangle-fill', title: 'Warning' },
        info:    { icon: 'bi-info-circle-fill',   title: 'Info' }
    };

    const c = config[type] || config.info;

    const toast = document.createElement('div');
    toast.className = `toast-notif toast-${type}`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="bi ${c.icon}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${c.title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="dismissToast(this.parentElement)">
            <i class="bi bi-x-lg"></i>
        </button>
        <div class="toast-progress" style="animation-duration:${duration}ms"></div>
    `;

    container.appendChild(toast);

    // Auto dismiss
    const timer = setTimeout(() => dismissToast(toast), duration);

    // Pause progress on hover
    toast.addEventListener('mouseenter', () => {
        toast.querySelector('.toast-progress').style.animationPlayState = 'paused';
        clearTimeout(timer);
    });

    toast.addEventListener('mouseleave', () => {
        toast.querySelector('.toast-progress').style.animationPlayState = 'running';
        setTimeout(() => dismissToast(toast), 2000);
    });

    // Click to dismiss
    toast.addEventListener('click', (e) => {
        if(!e.target.closest('.toast-close')){
            dismissToast(toast);
        }
    });
}

/**
 * Dismiss a toast with slide-out animation
 */
function dismissToast(el){
    if(!el || el.classList.contains('toast-dismissing')) return;
    el.classList.add('toast-dismissing');
    el.style.animation = 'toastSlideOut 0.35s cubic-bezier(0.16,1,0.3,1) forwards';
    setTimeout(() => el.remove(), 350);
}

<?php if($__notif): ?>
// Auto-show notification from PHP session
document.addEventListener('DOMContentLoaded', function(){
    showToast(
        '<?= htmlspecialchars($__notif['type']) ?>',
        '<?= htmlspecialchars($__notif['message']) ?>'
    );
});
<?php endif; ?>
</script>
