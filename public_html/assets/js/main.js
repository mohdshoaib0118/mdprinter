/**
 * MD Printers - Main Interactive JavaScript & Modal Engine
 */

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
    // 0. Auto Initialize Gooey Liquid Blob Buttons
    document.querySelectorAll('.btn, .blob-btn').forEach(btn => {
        if (!btn.querySelector('.blob-btn__inner') && !btn.querySelector('.btn-inner')) {
            const inner = document.createElement('span');
            inner.className = 'blob-btn__inner';
            inner.innerHTML = `
                <span class="blob-btn__blobs">
                    <span class="blob-btn__blob"></span>
                    <span class="blob-btn__blob"></span>
                    <span class="blob-btn__blob"></span>
                    <span class="blob-btn__blob"></span>
                </span>
            `;
            btn.appendChild(inner);
        }
    });

    // 1. Mobile Menu Toggle with Backdrop Overlay
    const mobileToggle = document.querySelector('.mobile-toggle');
    const navMenu = document.querySelector('.nav-menu');

    if (mobileToggle && navMenu) {
        let navBackdrop = document.querySelector('.nav-backdrop');
        if (!navBackdrop) {
            navBackdrop = document.createElement('div');
            navBackdrop.className = 'nav-backdrop';
            document.body.appendChild(navBackdrop);
        }

        const toggleNav = (show) => {
            const isActive = show !== undefined ? show : !navMenu.classList.contains('active');
            if (isActive) {
                navMenu.classList.add('active');
                navBackdrop.classList.add('active');
                document.body.classList.add('nav-open');
            } else {
                navMenu.classList.remove('active');
                navBackdrop.classList.remove('active');
                document.body.classList.remove('nav-open');
            }
            const icon = mobileToggle.querySelector('i');
            if (icon) {
                if (isActive) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            }
        };

        mobileToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleNav();
        });

        navBackdrop.addEventListener('click', () => toggleNav(false));

        navMenu.querySelectorAll('a, button:not(.mobile-toggle)').forEach(clickable => {
            clickable.addEventListener('click', () => {
                if (window.innerWidth <= 992) {
                    toggleNav(false);
                }
            });
        });

        window.addEventListener('resize', () => {
            if (window.innerWidth > 992 && navMenu.classList.contains('active')) {
                toggleNav(false);
            }
        });
    }

    // 2. Interactive Modal Engine (Quick Quote, Callback, Track Order, Sample Kit)
    const modalTriggers = document.querySelectorAll('[data-modal]');
    const modalCloses   = document.querySelectorAll('[data-close-modal], .site-modal-close');
    const modalBackdrops = document.querySelectorAll('.site-modal-backdrop');

    const openModal = (modalId, serviceName = null) => {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            // Auto select product service if passed
            if (serviceName) {
                const serviceSelect = modal.querySelector('#modal_product_service');
                if (serviceSelect) {
                    for (let option of serviceSelect.options) {
                        if (option.value.toLowerCase().includes(serviceName.toLowerCase()) || option.text.toLowerCase().includes(serviceName.toLowerCase())) {
                            option.selected = true;
                            break;
                        }
                    }
                }
            }
        }
    };

    const closeModal = (modal) => {
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    };

    modalTriggers.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modalType = btn.getAttribute('data-modal');
            const serviceName = btn.getAttribute('data-service');
            
            let modalId = 'quickQuoteModal';
            if (modalType === 'callback') modalId = 'callbackModal';
            if (modalType === 'track-order') modalId = 'trackOrderModal';
            if (modalType === 'sample-kit') modalId = 'sampleKitModal';
            if (modalType === 'quick-quote') modalId = 'quickQuoteModal';

            openModal(modalId, serviceName);
        });
    });

    modalCloses.forEach(closeBtn => {
        closeBtn.addEventListener('click', () => {
            const targetId = closeBtn.getAttribute('data-close-modal');
            if (targetId) {
                closeModal(document.getElementById(targetId));
            } else {
                closeModal(closeBtn.closest('.site-modal-backdrop'));
            }
        });
    });

    modalBackdrops.forEach(backdrop => {
        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) {
                closeModal(backdrop);
            }
        });
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modalBackdrops.forEach(backdrop => closeModal(backdrop));
        }
    });

    // 3. Track Order AJAX Handler
    const trackOrderForm = document.getElementById('trackOrderForm');
    const trackResultContainer = document.getElementById('trackResultContainer');

    if (trackOrderForm && trackResultContainer) {
        trackOrderForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = trackOrderForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Searching...';

            trackResultContainer.style.display = 'none';

            try {
                const formData = new FormData(trackOrderForm);
                const response = await fetch(trackOrderForm.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                trackResultContainer.style.display = 'block';

                if (result.success && result.data) {
                    const data = result.data;
                    let statusBadgeColor = '#0085b2';
                    if (data.status === 'Quoted') statusBadgeColor = '#c99e50';
                    if (data.status === 'In Progress' || data.status === 'Won') statusBadgeColor = '#ee0083';
                    if (data.status === 'Closed' || data.status === 'Contacted') statusBadgeColor = '#10B981';

                    trackResultContainer.innerHTML = `
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #cbd5e1; padding-bottom: 12px; margin-bottom: 14px;">
                            <div>
                                <strong style="font-size: 1.1rem; color: #0f172a;">${data.ref_no}</strong>
                                <span style="display: block; font-size: 0.8rem; color: #64748b;">Logged on ${data.created_at}</span>
                            </div>
                            <span style="background: ${statusBadgeColor}; color: #ffffff; padding: 4px 12px; border-radius: 12px; font-weight: 800; font-size: 0.85rem;">${data.status}</span>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <span style="display: block; font-size: 0.82rem; color: #64748b;">Customer:</span>
                            <strong style="color: #0f172a;">${data.customer_name}</strong>
                        </div>
                        <div style="margin-bottom: 12px;">
                            <span style="display: block; font-size: 0.82rem; color: #64748b;">Requested Service:</span>
                            <span style="color: #0f172a; font-weight: 700;">${data.service}</span>
                        </div>
                        <div style="background: #ffffff; padding: 12px; border-radius: 8px; border: 1px dashed #cbd5e1; font-size: 0.88rem; color: #334155;">
                            <i class="fas fa-info-circle" style="color: var(--cmyk-cyan); margin-right: 6px;"></i> ${data.latest_note}
                        </div>
                    `;
                } else {
                    trackResultContainer.innerHTML = `
                        <div style="color: #ef4444; font-weight: 700; text-align: center; padding: 10px 0;">
                            <i class="fas fa-exclamation-triangle" style="font-size: 1.4rem; margin-bottom: 6px; display: block;"></i>
                            ${result.message || 'No enquiry found with that reference number.'}
                        </div>
                    `;
                }
            } catch (err) {
                trackResultContainer.style.display = 'block';
                trackResultContainer.innerHTML = '<div style="color: #ef4444; text-align: center;">Network error while fetching tracking status.</div>';
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            }
        });
    }

    // 4. Animated Counter Numbers
    const counters = document.querySelectorAll('.stat-number');
    let hasCounted = false;

    const runCounters = () => {
        counters.forEach(counter => {
            const targetText = counter.innerText;
            const numericValue = parseInt(targetText.replace(/\D/g, ''), 10);
            const suffix = targetText.replace(/[0-9]/g, '');

            if (isNaN(numericValue)) return;

            let current = 0;
            const increment = Math.ceil(numericValue / 40);
            const timer = setInterval(() => {
                current += increment;
                if (current >= numericValue) {
                    counter.innerText = numericValue.toLocaleString() + suffix;
                    clearInterval(timer);
                } else {
                    counter.innerText = current.toLocaleString() + suffix;
                }
            }, 30);
        });
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasCounted) {
                hasCounted = true;
                runCounters();
            }
        });
    }, { threshold: 0.5 });

    const statsStrip = document.querySelector('.stats-strip');
    if (statsStrip) {
        observer.observe(statsStrip);
    }

    // 5. Portfolio Category Filtering
    const filterBtns = document.querySelectorAll('.portfolio-filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    if (filterBtns.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('btn-primary'));
                filterBtns.forEach(b => b.classList.add('btn-outline'));
                btn.classList.remove('btn-outline');
                btn.classList.add('btn-primary');

                const filter = btn.getAttribute('data-filter');
                portfolioItems.forEach(item => {
                    if (filter === 'all' || item.getAttribute('data-category') === filter) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    }

    // 6. File Dropzone Display
    const dropzoneInput = document.querySelector('#artwork_file');
    const dropzoneLabel = document.querySelector('.dropzone-filename');

    if (dropzoneInput && dropzoneLabel) {
        dropzoneInput.addEventListener('change', (e) => {
            if (e.target.files && e.target.files.length > 0) {
                const name = e.target.files[0].name;
                const size = (e.target.files[0].size / (1024 * 1024)).toFixed(2);
                dropzoneLabel.innerHTML = `<i class="fas fa-file-check" style="color:var(--cmyk-cyan)"></i> <strong>${name}</strong> (${size} MB)`;
            }
        });
    }

    // 7. Universal AJAX Form Handler
    const ajaxForms = document.querySelectorAll('.ajax-form');
    ajaxForms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const submitBtn = form.querySelector('button[type="submit"]');
            const alertBox = form.querySelector('.form-alert');
            const originalBtnText = submitBtn ? submitBtn.innerHTML : 'Submit';

            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing Request...';
            }

            if (alertBox) {
                alertBox.style.display = 'none';
                alertBox.className = 'form-alert';
            }

            try {
                const formData = new FormData(form);
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.success) {
                    if (result.whatsapp_url) {
                        try {
                            window.openWhatsApp(result.whatsapp_url);
                        } catch (e) {}
                    }

                    if (alertBox) {
                        alertBox.className = 'form-alert alert-success';
                        alertBox.style.display = 'block';
                        let waHtml = '';
                        if (result.whatsapp_url) {
                            const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
                            const targetAttr = isMobile ? '_self' : '_blank';
                            waHtml = `<div style="margin-top: 10px;"><a href="${result.whatsapp_url}" target="${targetAttr}" onclick="event.preventDefault(); window.openWhatsApp('${result.whatsapp_url}');" style="background: #25D366; color: #ffffff; font-weight: 800; padding: 8px 16px; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; font-size: 0.9rem;"><i class="fab fa-whatsapp" style="font-size: 1.1rem;"></i> Send Specs to WhatsApp Now <i class="fas fa-arrow-right"></i></a></div>`;
                        }
                        alertBox.innerHTML = `<i class="fas fa-check-circle"></i> ${result.message} ${result.ref_no ? '<strong>[Ref: ' + result.ref_no + ']</strong>' : ''}${waHtml}`;
                    } else {
                        alert(result.message);
                    }
                    form.reset();
                    if (dropzoneLabel) {
                        dropzoneLabel.innerHTML = 'Drag & drop artwork file (PDF, AI, PSD, ZIP) or click to browse';
                    }
                } else {
                    if (alertBox) {
                        alertBox.className = 'form-alert alert-danger';
                        alertBox.style.display = 'block';
                        alertBox.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${result.message}`;
                    } else {
                        alert(result.message);
                    }
                }
            } catch (error) {
                if (alertBox) {
                    alertBox.className = 'form-alert alert-danger';
                    alertBox.style.display = 'block';
                    alertBox.innerHTML = '<i class="fas fa-exclamation-circle"></i> Network error occurred. Please try again.';
                }
            } finally {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnText;
                }
            }
        });
    });

    // 8. Hero Banner Slider Carousel Engine
    const slides = document.querySelectorAll('.banner-slide');
    const dots = document.querySelectorAll('.slider-dot');
    const prevBtn = document.querySelector('.slider-arrow.prev');
    const nextBtn = document.querySelector('.slider-arrow.next');
    let currentSlide = 0;
    let slideInterval = null;

    if (slides.length > 0) {
        const goToSlide = (n) => {
            slides[currentSlide].classList.remove('active');
            if (dots[currentSlide]) dots[currentSlide].classList.remove('active');

            currentSlide = (n + slides.length) % slides.length;

            slides[currentSlide].classList.add('active');
            if (dots[currentSlide]) dots[currentSlide].classList.add('active');
        };

        const nextSlide = () => goToSlide(currentSlide + 1);
        const prevSlide = () => goToSlide(currentSlide - 1);

        if (nextBtn) nextBtn.addEventListener('click', () => { nextSlide(); resetTimer(); });
        if (prevBtn) prevBtn.addEventListener('click', () => { prevSlide(); resetTimer(); });

        dots.forEach((dot, idx) => {
            dot.addEventListener('click', () => {
                goToSlide(idx);
                resetTimer();
            });
        });

        const startTimer = () => {
            slideInterval = setInterval(nextSlide, 4500);
        };

        const resetTimer = () => {
            clearInterval(slideInterval);
            startTimer();
        };

        startTimer();

        // Pause on mouse hover
        const sliderWrapper = document.querySelector('.hero-banner-slider-wrapper');
        if (sliderWrapper) {
            sliderWrapper.addEventListener('mouseenter', () => clearInterval(slideInterval));
            sliderWrapper.addEventListener('mouseleave', () => startTimer());
        }
    }

    // 9. Scroll Reveal Animation Engine
    const revealElements = document.querySelectorAll('.reveal-on-scroll, .card-feature, .section-title, .grid-3 > div, .grid-4 > div');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('revealed');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12 });

    revealElements.forEach(el => {
        el.classList.add('reveal-prep');
        revealObserver.observe(el);
    });

    // 10. Interactive Product Layout View Switcher (3 Grid / 4 Grid / List View)
    const viewToggleBtns = document.querySelectorAll('.view-toggle-btn');
    viewToggleBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const parent = btn.closest('section') || btn.closest('.container');
            const container = parent ? parent.querySelector('.product-grid-container') : document.querySelector('.product-grid-container');
            if (!container) return;

            const mode = btn.getAttribute('data-grid');

            const group = btn.closest('.view-toggle-group');
            if (group) {
                group.querySelectorAll('.view-toggle-btn').forEach(b => b.classList.remove('active'));
            }
            btn.classList.add('active');

            container.classList.remove('grid-3', 'grid-4', 'view-list');
            container.classList.add(mode);
        });
    });

    // 11. International Telephone Input Initialization with Country Flags
    const initPhoneInputs = () => {
        if (typeof window.intlTelInput === 'undefined') return;
        const phoneInputs = document.querySelectorAll('input[type="tel"], input[name="phone"]');
        phoneInputs.forEach(input => {
            if (input.dataset.itiInitialized) return;
            input.dataset.itiInitialized = "true";
            
            window.intlTelInput(input, {
                initialCountry: "in",
                onlyCountries: ["in"],
                allowDropdown: false,
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/18.2.1/js/utils.js"
            });
        });
    };
    initPhoneInputs();
});
