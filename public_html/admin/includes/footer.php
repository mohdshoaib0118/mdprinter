    </div> <!-- end admin-content -->
</div> <!-- end admin-main -->

<!-- Custom Admin Delete Confirmation Modal Popup -->
<div id="adminDeleteConfirmModal" class="admin-modal-backdrop" style="display: none;">
    <div class="admin-modal-dialog" style="max-width: 450px;">
        <div class="admin-modal-content">
            <div class="admin-modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: #ffffff;">
                <h3 class="admin-modal-title" style="color: #ffffff;"><i class="fas fa-exclamation-triangle"></i> Confirm Delete Action</h3>
                <button type="button" class="admin-modal-close" data-close-modal="adminDeleteConfirmModal" style="color: #ffffff;">&times;</button>
            </div>
            <div class="admin-modal-body" style="text-align: center; padding: 28px 24px;">
                <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(239, 68, 68, 0.1); color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 1.8rem; margin: 0 auto 16px auto;">
                    <i class="fas fa-trash-alt"></i>
                </div>
                <h4 style="font-size: 1.15rem; font-weight: 800; color: #0f172a; margin-bottom: 8px;">Are you sure?</h4>
                <p id="adminDeleteConfirmText" style="color: #64748b; font-size: 0.9rem; margin-bottom: 24px;">
                    This item will be permanently removed from your system. This action cannot be undone.
                </p>
                <div style="display: flex; justify-content: center; gap: 12px;">
                    <button type="button" class="btn btn-outline" data-close-modal="adminDeleteConfirmModal" style="padding: 10px 20px; font-weight: 700; cursor: pointer;">Cancel</button>
                    <button type="button" id="adminDeleteConfirmBtn" class="btn" style="background: #ef4444; color: #ffffff; padding: 10px 22px; font-weight: 800; border: none; cursor: pointer; border-radius: 8px;">
                        <i class="fas fa-trash"></i> Yes, Delete Permanently
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.openWhatsApp = function(url) {
    if (!url) return;
    const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    if (isMobile) {
        window.location.href = url;
    } else {
        window.open(url, '_blank');
    }
};

document.addEventListener('click', function(e) {
    const link = e.target.closest('a[href*="wa.me"], a[href*="whatsapp.com"], a[href*="api.whatsapp.com"]');
    if (link) {
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        if (isMobile) {
            e.preventDefault();
            window.location.href = link.href;
        } else {
            link.setAttribute('target', '_blank');
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Admin Modal Engine Helpers
    window.openAdminModal = (modalId) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    };

    window.closeAdminModal = (modalId) => {
        const modal = typeof modalId === 'string' ? document.getElementById(modalId) : modalId;
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    // Custom Delete Confirmation Modal Helper
    window.confirmAdminDelete = (event, targetUrlOrForm, customMessage) => {
        if (event) event.preventDefault();
        const modal = document.getElementById('adminDeleteConfirmModal');
        const deleteBtn = document.getElementById('adminDeleteConfirmBtn');
        const messageEl = document.getElementById('adminDeleteConfirmText');

        if (customMessage && messageEl) {
            messageEl.innerText = customMessage;
        } else if (messageEl) {
            messageEl.innerText = 'This item will be permanently removed from your system. This action cannot be undone.';
        }

        if (deleteBtn) {
            deleteBtn.onclick = function() {
                if (typeof targetUrlOrForm === 'string') {
                    window.location.href = targetUrlOrForm;
                } else if (targetUrlOrForm && targetUrlOrForm.tagName === 'FORM') {
                    targetUrlOrForm.submit();
                } else if (typeof targetUrlOrForm === 'function') {
                    targetUrlOrForm();
                }
            };
        }

        openAdminModal('adminDeleteConfirmModal');
        return false;
    };

    document.querySelectorAll('[data-open-modal]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalId = btn.getAttribute('data-open-modal');
            openAdminModal(modalId);
        });
    });

    document.querySelectorAll('[data-close-modal], .admin-modal-close').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const modalId = btn.getAttribute('data-close-modal');
            if (modalId) {
                closeAdminModal(modalId);
            } else {
                closeAdminModal(btn.closest('.admin-modal-backdrop'));
            }
        });
    });

    document.querySelectorAll('.admin-modal-backdrop').forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeAdminModal(backdrop);
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.admin-modal-backdrop').forEach(b => closeAdminModal(b));
        }
    });
});
</script>
</body>
</html>
