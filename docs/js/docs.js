const NAV = [
    {
        title: 'Start',
        items: [
            { href: 'index.html', label: 'Introduction' },
            { href: 'install.html', label: 'Install' },
            { href: 'tutorial.html', label: 'Tutorial' },
        ],
    },
    {
        title: 'Core',
        items: [
            { href: 'architecture.html', label: 'Architecture' },
            { href: 'routing.html', label: 'Routing' },
            { href: 'controllers.html', label: 'Controllers & HTTP' },
            { href: 'middleware.html', label: 'Middleware' },
        ],
    },
    {
        title: 'Layers',
        items: [
            { href: 'models.html', label: 'Models & database' },
            { href: 'views.html', label: 'Blade & views' },
            { href: 'validation.html', label: 'Validation' },
            { href: 'auth.html', label: 'Authentication' },
            { href: 'cli.html', label: 'CLI' },
        ],
    },
];

function currentPage() {
    const file = window.location.pathname.split('/').pop();
    return file === '' ? 'index.html' : file;
}

function renderChrome() {
    const page = currentPage();
    const header = document.getElementById('topbar');
    const sidebar = document.getElementById('sidebar');

    if (header) {
        header.innerHTML = `
            <a class="brand" href="index.html">
                <span class="brand-mark">L</span>
                <span>
                    <strong>Lite MVC</strong>
                    <small>Framework docs</small>
                </span>
            </a>
            <div class="top-links">
                <button class="menu-btn" type="button" data-toggle-nav>Menu</button>
                <a href="https://github.com/mortenaho/php-mvc-lite">GitHub</a>
                <a class="cta" href="install.html">Quick start</a>
            </div>
        `;
    }

    if (sidebar) {
        sidebar.innerHTML = NAV.map((group) => `
            <h4>${group.title}</h4>
            ${group.items.map((item) => `
                <a href="${item.href}" class="${item.href === page ? 'is-active' : ''}">${item.label}</a>
            `).join('')}
        `).join('');
    }

    document.querySelector('[data-toggle-nav]')?.addEventListener('click', () => {
        sidebar?.classList.toggle('is-open');
    });
}

document.addEventListener('DOMContentLoaded', renderChrome);
