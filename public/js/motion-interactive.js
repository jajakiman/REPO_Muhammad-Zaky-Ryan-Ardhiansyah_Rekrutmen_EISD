(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        var prefersReduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (prefersReduced) return;
        if (typeof window.Motion === 'undefined') return;

        var animate = window.Motion.animate;
        var inView = window.Motion.inView;

        // 1. Hero headline & badge subtle reveal
        var heroHeading = document.querySelector('.hero h1');
        if (heroHeading) {
            animate(heroHeading, { opacity: [0, 1], y: [16, 0] }, { duration: 0.5, easing: 'ease-out' });
        }

        var heroActions = document.querySelector('.hero a.button');
        if (heroActions) {
            animate('.hero .actions a', { opacity: [0, 1], scale: [0.96, 1] }, { duration: 0.4, delay: 0.15, easing: 'ease-out' });
        }

        var heroOverhang = document.querySelector('.hero .overflow-hidden.rounded-2xl');
        if (heroOverhang) {
            animate(heroOverhang, { opacity: [0, 1], y: [24, 0] }, { duration: 0.6, delay: 0.25, easing: 'ease-out' });
        }

        // 2. Stat cards on scroll reveal
        var statCards = document.querySelectorAll('.stat-grid .service-card');
        if (statCards.length > 0 && typeof inView === 'function') {
            inView('.stat-grid', function () {
                statCards.forEach(function (card, index) {
                    animate(card, { opacity: [0, 1], y: [20, 0] }, { duration: 0.4, delay: index * 0.08, easing: 'ease-out' });
                });
            });
        }

        // 3. Workflow steps on scroll reveal
        var workflowCards = document.querySelectorAll('.workflow-grid .workflow-card');
        if (workflowCards.length > 0 && typeof inView === 'function') {
            inView('.workflow-grid', function () {
                workflowCards.forEach(function (card, index) {
                    animate(card, { opacity: [0, 1], y: [20, 0] }, { duration: 0.4, delay: index * 0.08, easing: 'ease-out' });
                });
            });
        }

        // 4. Subtle button hover tactile feedback
        document.querySelectorAll('a.button-primary, button.button-primary').forEach(function (btn) {
            btn.addEventListener('mouseenter', function () {
                animate(btn, { scale: 1.02 }, { duration: 0.15, easing: 'ease-out' });
            });
            btn.addEventListener('mouseleave', function () {
                animate(btn, { scale: 1 }, { duration: 0.15, easing: 'ease-out' });
            });
        });
    });
})();
