(function () {
    'use strict';

    const input = document.getElementById('dwf-search-input');
    const btn = document.getElementById('dwf-search-btn');
    const previewText = document.getElementById('dwf-preview-text');
    const resultsBox = document.getElementById('dwf-results');
    const loadingBox = document.getElementById('dwf-loading');
    const filterBtns = document.querySelectorAll('.dwf-filter-btn');

    if (!input || !resultsBox) return;

    let currentCategory = 'all';
    let debounceTimer = null;
    const loadedFonts = new Set();

    function loadGoogleFont(family) {
        if (loadedFonts.has(family)) return;
        loadedFonts.add(family);
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://fonts.googleapis.com/css2?family=' +
            encodeURIComponent(family).replace(/%20/g, '+') + '&display=swap';
        document.head.appendChild(link);
    }

    function renderResults(fonts) {
        resultsBox.innerHTML = '';

        if (!fonts || fonts.length === 0) {
            resultsBox.innerHTML = '<div class="dwf-empty-state">No se encontraron fuentes. Probá con otro término.</div>';
            return;
        }

        const sample = previewText.value || 'DigitalWave — Diseño & Desarrollo Web';

        fonts.forEach((font) => {
            loadGoogleFont(font.family);

            const card = document.createElement('div');
            card.className = 'dwf-font-card';

            const cssFamily = font.family.replace(/'/g, "\\'");

            card.innerHTML = `
                <div class="dwf-font-name">${escapeHtml(font.family)}</div>
                <span class="dwf-font-category">${escapeHtml(font.category)}</span>
                <div class="dwf-font-sample" style="font-family: '${cssFamily}', sans-serif;">${escapeHtml(sample)}</div>
                <div class="dwf-font-variants">Variantes: ${font.variants.map(escapeHtml).join(', ')}</div>
            `;

            resultsBox.appendChild(card);
        });
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    async function searchFonts() {
        loadingBox.style.display = 'block';
        resultsBox.innerHTML = '';

        const formData = new URLSearchParams();
        formData.append('action', 'dwf_search_fonts');
        formData.append('nonce', dwfAjax.nonce);
        formData.append('search', input.value.trim());
        formData.append('category', currentCategory);

        try {
            const response = await fetch(dwfAjax.ajaxUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: formData.toString(),
            });

            const data = await response.json();

            loadingBox.style.display = 'none';

            if (data.success) {
                renderResults(data.data.fonts);
            } else {
                resultsBox.innerHTML = `<div class="dwf-empty-state">${escapeHtml(data.data.message || 'Ocurrió un error.')}</div>`;
            }
        } catch (err) {
            loadingBox.style.display = 'none';
            resultsBox.innerHTML = '<div class="dwf-empty-state">Error de conexión. Intentá de nuevo.</div>';
        }
    }

    // Búsqueda con debounce mientras se escribe
    input.addEventListener('input', () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(searchFonts, 800);
    });

    // Botón de búsqueda manual
    btn.addEventListener('click', searchFonts);

    // Actualizar preview en vivo al cambiar el texto de muestra
    previewText.addEventListener('input', () => {
        const sample = previewText.value || 'DigitalWave — Diseño & Desarrollo Web';
        document.querySelectorAll('.dwf-font-sample').forEach((el) => {
            el.textContent = sample;
        });
    });

    // Filtros por categoría
    filterBtns.forEach((filterBtn) => {
        filterBtn.addEventListener('click', () => {
            filterBtns.forEach((b) => b.classList.remove('active'));
            filterBtn.classList.add('active');
            currentCategory = filterBtn.dataset.category;
            searchFonts();
        });
    });

    // Carga inicial: mostrar fuentes populares
    searchFonts();
})();
