const NAV = [
    {
        title: 'شروع',
        items: [
            { href: 'index.html', label: 'معرفی' },
            { href: 'install.html', label: 'نصب و راه‌اندازی' },
            { href: 'tutorial.html', label: 'آموزش عملی' },
        ],
    },
    {
        title: 'هسته',
        items: [
            { href: 'architecture.html', label: 'معماری' },
            { href: 'routing.html', label: 'مسیریابی' },
            { href: 'controllers.html', label: 'کنترلر و HTTP' },
            { href: 'middleware.html', label: 'میدل‌ویر' },
        ],
    },
    {
        title: 'لایه‌ها',
        items: [
            { href: 'models.html', label: 'مدل و دیتابیس' },
            { href: 'views.html', label: 'Blade و ویو' },
            { href: 'validation.html', label: 'اعتبارسنجی' },
            { href: 'cli.html', label: 'خط فرمان' },
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
                    <small>مستندات فریم‌ورک</small>
                </span>
            </a>
            <div class="top-links">
                <button class="menu-btn" type="button" data-toggle-nav>فهرست</button>
                <a href="https://github.com/mortenaho/php-mvc-lite">گیت‌هاب</a>
                <a class="cta" href="install.html">شروع سریع</a>
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
