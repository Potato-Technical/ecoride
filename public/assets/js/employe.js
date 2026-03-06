document.addEventListener('DOMContentLoaded', () => {
    const cards = Array.from(document.querySelectorAll('.emp-card'));
    const tabs = Array.from(document.querySelectorAll('.emp-tab'));
    const sidebarLinks = Array.from(document.querySelectorAll('[data-sidebar-filter]'));
    const sections = Array.from(document.querySelectorAll('.emp-section'));

    const kpiIncidents = document.getElementById('kpi-incidents');
    const kpiAvis = document.getElementById('kpi-avis');
    const kpiTotal = document.getElementById('kpi-total');

    const sectionCountIncidents = document.getElementById('section-count-incidents');
    const sectionCountAvis = document.getElementById('section-count-avis');

    if (!cards.length) {
        return;
    }

    let currentTypeFilter = 'all';
    let currentSidebarFilter = 'overview';

    function setActiveTab(filter) {
        tabs.forEach((tab) => {
            tab.classList.toggle('emp-tab--active', tab.dataset.filter === filter);
        });
    }

    function setActiveSidebar(filter) {
        sidebarLinks.forEach((link) => {
            link.classList.toggle('active', link.dataset.sidebarFilter === filter);
        });
    }

    function updateKpis() {
        const visibleCards = cards.filter((card) => !card.hidden);
        const visibleIncidents = visibleCards.filter((card) => card.dataset.type === 'incident');
        const visibleAvis = visibleCards.filter((card) => card.dataset.type === 'avis');

        if (kpiIncidents) {
            kpiIncidents.textContent = String(visibleIncidents.length);
        }

        if (kpiAvis) {
            kpiAvis.textContent = String(visibleAvis.length);
        }

        if (kpiTotal) {
            kpiTotal.textContent = String(visibleCards.length);
        }
    }

    function updateSectionVisibility() {
        sections.forEach((section) => {
            const visibleCards = section.querySelectorAll('.emp-card:not([hidden])');
            const emptyBlock = section.querySelector('.emp-empty');
            const list = section.querySelector('.emp-list');

            section.hidden = false;

            if (section.id === 'incidents' && sectionCountIncidents) {
                sectionCountIncidents.textContent = String(visibleCards.length);
            }

            if (section.id === 'avis' && sectionCountAvis) {
                sectionCountAvis.textContent = String(visibleCards.length);
            }

            if (visibleCards.length > 0) {
                if (emptyBlock) {
                    emptyBlock.hidden = true;
                }
                if (list) {
                    list.hidden = false;
                }
                return;
            }

            if (list) {
                list.hidden = true;
            }

            if (emptyBlock) {
                emptyBlock.hidden = false;
            }
        });
    }

    function matchesType(card) {
        if (currentTypeFilter === 'all') {
            return true;
        }

        return card.dataset.type === currentTypeFilter;
    }

    function matchesSidebar(card) {
        if (currentSidebarFilter === 'overview') {
            return card.dataset.history !== 'true';
        }

        if (currentSidebarFilter === 'assigned') {
            return card.dataset.assigned === 'true' && card.dataset.history !== 'true';
        }

        if (currentSidebarFilter === 'history') {
            return card.dataset.history === 'true';
        }

        return true;
    }

    function applyFilters() {
        cards.forEach((card) => {
            const show = matchesType(card) && matchesSidebar(card);
            card.hidden = !show;
        });

        updateSectionVisibility();
        updateKpis();
    }

    tabs.forEach((tab) => {
        tab.addEventListener('click', () => {
            currentTypeFilter = tab.dataset.filter || 'all';
            setActiveTab(currentTypeFilter);
            applyFilters();
        });
    });

    sidebarLinks.forEach((link) => {
        link.addEventListener('click', (event) => {
            event.preventDefault();

            currentSidebarFilter = link.dataset.sidebarFilter || 'all';
            setActiveSidebar(currentSidebarFilter);
            applyFilters();

            const targetId = link.getAttribute('href');
            if (targetId && targetId.startsWith('#')) {
                const target = document.querySelector(targetId);
                if (target) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    setActiveTab(currentTypeFilter);
    setActiveSidebar(currentSidebarFilter);
    applyFilters();
});