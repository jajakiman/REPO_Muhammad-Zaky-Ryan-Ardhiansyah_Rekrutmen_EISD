(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;

        var hasMotion = typeof window.Motion !== 'undefined' && typeof window.Motion.animate === 'function';
        var animate = hasMotion ? window.Motion.animate : null;
        var inView = hasMotion && typeof window.Motion.inView === 'function' ? window.Motion.inView : null;

        // 1. Hero Animations
        if (animate) {
            var heroHeading = document.querySelector('.hero h1');
            if (heroHeading) {
                animate(heroHeading, { opacity: [0, 1], y: [16, 0] }, { duration: 0.6, easing: 'ease-out' });
            }

            var heroButtons = document.querySelectorAll('.hero a');
            if (heroButtons.length > 0) {
                animate(heroButtons, { opacity: [0, 1], scale: [0.96, 1] }, { duration: 0.4, delay: 0.15, easing: 'ease-out' });
            }

            var heroOverhang = document.querySelector('.hero .overflow-hidden.rounded-2xl');
            if (heroOverhang) {
                animate(heroOverhang, { opacity: [0, 1], y: [24, 0] }, { duration: 0.6, delay: 0.25, easing: 'ease-out' });
            }
        }

        // 2. Count-Up Animation for Stat Numbers
        var statNumbers = document.querySelectorAll('.stat-number');
        if (statNumbers.length > 0) {
            var animatedStats = false;
            var runCountUp = function () {
                if (animatedStats) return;
                animatedStats = true;

                statNumbers.forEach(function (el) {
                    var target = parseInt(el.textContent.trim(), 10);
                    if (isNaN(target) || target <= 0) return;

                    var start = 0;
                    var duration = 1200; // 1.2s
                    var startTime = null;

                    var step = function (timestamp) {
                        if (!startTime) startTime = timestamp;
                        var progress = Math.min((timestamp - startTime) / duration, 1);
                        // easeOutQuad
                        var ease = 1 - (1 - progress) * (1 - progress);
                        el.textContent = Math.floor(ease * target);
                        if (progress < 1) {
                            window.requestAnimationFrame(step);
                        } else {
                            el.textContent = target;
                        }
                    };
                    window.requestAnimationFrame(step);
                });
            };

            if (inView) {
                inView('.stat-grid', runCountUp);
            } else if ('IntersectionObserver' in window) {
                var statObserver = new IntersectionObserver(function (entries) {
                    if (entries[0].isIntersecting) {
                        runCountUp();
                        statObserver.disconnect();
                    }
                }, { threshold: 0.2 });
                var statGrid = document.querySelector('.stat-grid');
                if (statGrid) statObserver.observe(statGrid);
            } else {
                runCountUp();
            }
        }

        // 3. Staggered Scroll-Reveal for Workflow Cards
        var workflowCards = document.querySelectorAll('.workflow-grid .workflow-card');
        if (workflowCards.length > 0) {
            if (inView && animate) {
                inView('.workflow-grid', function () {
                    workflowCards.forEach(function (card, idx) {
                        animate(card, { opacity: [0, 1], y: [24, 0] }, { duration: 0.45, delay: idx * 0.1, easing: 'ease-out' });
                    });
                });
            }
        }

        // 4. Subtle Card Hover Lift
        document.querySelectorAll('.service-card, .workflow-card').forEach(function (card) {
            card.style.transition = 'transform 200ms ease, box-shadow 200ms ease';
            card.addEventListener('mouseenter', function () {
                card.style.transform = 'translateY(-4px)';
            });
            card.addEventListener('mouseleave', function () {
                card.style.transform = 'translateY(0)';
            });
        });

        // 5. Button Press Feedback
        document.querySelectorAll('a.button-primary, button.button-primary').forEach(function (btn) {
            btn.addEventListener('mousedown', function () {
                btn.style.transform = 'scale(0.98)';
            });
            btn.addEventListener('mouseup', function () {
                btn.style.transform = '';
            });
            btn.addEventListener('mouseleave', function () {
                btn.style.transform = '';
            });
        });
    });
})();
