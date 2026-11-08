const root = document.documentElement;
const read = (key) => {
    try { return JSON.parse(localStorage.getItem(key)); } catch { return null; }
};
const write = (key, value) => {
    try { localStorage.setItem(key, JSON.stringify(value)); } catch {}
};
const remove = (key) => {
    try { localStorage.removeItem(key); } catch {}
};
const consentKey = 'pochin:consent:v1';
const motionKey = 'pochin:motion:v1';
const faultKey = 'pochin:fault:v1';
const reducedPreference = matchMedia('(prefers-reduced-motion: reduce)');
let consent = read(consentKey);
if (!consent || consent.version !== 1 || consent.necessary !== true || typeof consent.preferences !== 'boolean' || !Number.isFinite(consent.at) || consent.at > Date.now() || Date.now() - consent.at > 180 * 86400000) consent = null;
let temporaryMotion = null;
const motionAllowed = () => !reducedPreference.matches && root.dataset.motion !== 'reduced';
const toast = document.querySelector('[data-toast]');
let toastTimer;
function notify(message) {
    if (!toast) return;
    clearTimeout(toastTimer);
    toast.textContent = message;
    toast.hidden = false;
    toastTimer = setTimeout(() => { toast.hidden = true; }, 5000);
}
function applyMotion() {
    const stored = consent?.preferences ? read(motionKey) : null;
    const reduced = reducedPreference.matches || temporaryMotion === 'reduced' || (temporaryMotion === null && stored === 'reduced');
    root.dataset.motion = reduced ? 'reduced' : 'full';
    document.querySelectorAll('[data-motion-toggle]').forEach((button) => {
        button.textContent = reduced ? 'Анимации уменьшены' : 'Уменьшить анимации';
        button.setAttribute('aria-pressed', String(reduced));
    });
    if (reduced) document.querySelectorAll('.reveal').forEach((el) => el.classList.add('is-visible'));
}
applyMotion();
root.classList.add('js');
reducedPreference.addEventListener('change', applyMotion);
const banner = document.querySelector('[data-cookie-banner]');
const cookieDialog = document.getElementById('cookie-dialog');
if (banner) banner.hidden = Boolean(consent);
function openDialog(dialog) {
    if (!dialog || dialog.open) return;
    if (typeof dialog.showModal === 'function') dialog.showModal();
    else dialog.setAttribute('open', '');
}
function closeDialog(dialog) {
    if (!dialog) return;
    if (typeof dialog.close === 'function') dialog.close();
    else dialog.removeAttribute('open');
}
function saveConsent(preferences) {
    consent = { version: 1, necessary: true, preferences, at: Date.now() };
    write(consentKey, consent);
    if (!preferences) { remove(motionKey); remove(faultKey); temporaryMotion = null; }
    if (banner) banner.hidden = true;
    closeDialog(cookieDialog);
    applyMotion();
    notify('Настройки сохранены.');
}
document.addEventListener('click', (event) => {
    const target = event.target;
    if (!(target instanceof Element)) return;
    if (target.closest('[data-cookie-open]')) {
        const preferences = cookieDialog?.querySelector('[data-cookie-preferences]');
        if (preferences) preferences.checked = consent?.preferences ?? false;
        openDialog(cookieDialog);
    }
    if (target.closest('[data-cookie-accept]')) saveConsent(true);
    if (target.closest('[data-cookie-necessary]')) saveConsent(false);
    if (target.closest('[data-cookie-save]')) saveConsent(Boolean(cookieDialog?.querySelector('[data-cookie-preferences]')?.checked));
    if (target.closest('[data-close-dialog]')) closeDialog(target.closest('dialog'));
    if (target.closest('[data-dismiss]')) target.closest('.notice')?.remove();
    if (target.closest('[data-motion-toggle]')) {
        if (reducedPreference.matches) { notify('Уменьшение движения включено в настройках вашего устройства.'); return; }
        temporaryMotion = root.dataset.motion === 'reduced' ? 'full' : 'reduced';
        if (consent?.preferences) write(motionKey, temporaryMotion);
        applyMotion();
    }
    const passwordButton = target.closest('[data-password-toggle]');
    if (passwordButton) {
        const input = document.getElementById(passwordButton.dataset.passwordToggle);
        if (!input) return;
        const shown = input.type === 'password';
        input.type = shown ? 'text' : 'password';
        passwordButton.setAttribute('aria-label', shown ? 'Скрыть пароль' : 'Показать пароль');
        passwordButton.setAttribute('aria-pressed', String(shown));
    }
});
document.querySelectorAll('dialog').forEach((dialog) => {
    dialog.addEventListener('click', (event) => {
        if (event.target !== dialog) return;
        const box = dialog.getBoundingClientRect();
        if (event.clientX < box.left || event.clientX > box.right || event.clientY < box.top || event.clientY > box.bottom) closeDialog(dialog);
    });
});
const menuButton = document.querySelector('[data-menu-toggle]');
const menu = document.getElementById('main-nav');
function closeMenu(restoreFocus = false) {
    if (!menuButton || !menu) return;
    menu.classList.remove('is-open');
    menuButton.setAttribute('aria-expanded', 'false');
    menuButton.setAttribute('aria-label', 'Открыть меню');
    if (restoreFocus) menuButton.focus();
}
menuButton?.addEventListener('click', () => {
    const opened = menuButton.getAttribute('aria-expanded') !== 'true';
    menuButton.setAttribute('aria-expanded', String(opened));
    menuButton.setAttribute('aria-label', opened ? 'Закрыть меню' : 'Открыть меню');
    menu?.classList.toggle('is-open', opened);
});
menu?.addEventListener('click', (event) => { if (event.target.closest('a')) closeMenu(); });
document.addEventListener('keydown', (event) => { if (event.key === 'Escape' && menu?.classList.contains('is-open')) closeMenu(true); });
document.addEventListener('click', (event) => { if (!event.target.closest('.site-header') && menu?.classList.contains('is-open')) closeMenu(); });
matchMedia('(min-width: 651px)').addEventListener('change', (event) => { if (event.matches) closeMenu(); });
const revealObserver = 'IntersectionObserver' in window ? new IntersectionObserver((entries) => {
    entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        revealObserver.unobserve(entry.target);
    });
}, { threshold: .08, rootMargin: '0px 0px -25px 0px' }) : null;
document.querySelectorAll('.reveal').forEach((element, index) => {
    element.style.setProperty('--delay', Math.min(index % 3, 2) * 75 + 'ms');
    if (!motionAllowed() || !revealObserver) element.classList.add('is-visible');
    else revealObserver.observe(element);
});
document.querySelectorAll('.image-frame img').forEach((img) => {
    const frame = img.closest('.image-frame');
    const loaded = () => {
        frame.classList.remove('is-loading');
        if (img.naturalWidth === 0) {
            img.hidden = true;
            if (!frame.querySelector('.image-failed')) {
                const fallback = document.createElement('p');
                fallback.className = 'image-failed';
                fallback.textContent = (img.alt ? img.alt + '. ' : '') + 'Изображение не удалось загрузить.';
                frame.append(fallback);
            }
        }
    };
    if (img.complete) loaded();
    else { frame.classList.add('is-loading'); img.addEventListener('load', loaded, { once: true }); img.addEventListener('error', loaded, { once: true }); }
});
const faultPicker = document.querySelector('[data-fault-picker]');
if (faultPicker) {
    const radios = [...faultPicker.querySelectorAll('[name=service]')];
    const saved = consent?.preferences ? read(faultKey) : null;
    const storedRadio = saved?.version === 1 ? radios.find((input) => input.value === saved.service) : null;
    if (storedRadio) storedRadio.checked = true;
    const update = (animate = false) => {
        const selected = radios.find((input) => input.checked);
        if (!selected) return;
        const title = faultPicker.querySelector('[data-fault-title]');
        title.textContent = selected.dataset.title;
        faultPicker.querySelector('[data-fault-price]').textContent = new Intl.NumberFormat('ru-RU').format(Number(selected.dataset.price)) + ' ₽';
        if (consent?.preferences) write(faultKey, { version: 1, service: selected.value });
        if (animate && motionAllowed() && title.animate) title.animate([{ opacity: .2, transform: 'translateY(5px)' }, { opacity: 1, transform: 'translateY(0)' }], { duration: 220, easing: 'ease-out' });
    };
    update();
    faultPicker.addEventListener('change', () => update(true));
}
const backTop = document.querySelector('[data-back-top]');
const reading = document.querySelector('[data-reading]');
const readingProgress = document.querySelector('[data-reading-progress]');
const process = document.querySelector('[data-process]');
let scrollPending = false;
function updateScroll() {
    root.classList.toggle('is-scrolled', scrollY > 12);
    backTop?.classList.toggle('is-visible', scrollY > 650);
    if (reading && readingProgress) {
        const box = reading.getBoundingClientRect();
        const fraction = Math.max(0, Math.min(1, -box.top / Math.max(1, box.height - innerHeight * .6)));
        readingProgress.style.transform = 'scaleX(' + fraction + ')';
    }
    if (process) {
        const box = process.getBoundingClientRect();
        const progress = motionAllowed() ? Math.max(0, Math.min(1, (innerHeight - box.top - 70) / (innerHeight * .8))) : 1;
        process.querySelector('[data-process-progress]').style.transform = 'scaleX(' + progress + ')';
    }
    scrollPending = false;
}
function scheduleScroll() { if (!scrollPending) { scrollPending = true; requestAnimationFrame(updateScroll); } }
addEventListener('scroll', scheduleScroll, { passive: true });
addEventListener('resize', scheduleScroll);
updateScroll();
const imageDialog = document.getElementById('image-dialog');
document.addEventListener('click', (event) => {
    const link = event.target.closest('[data-lightbox]');
    if (!link || !imageDialog || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
    event.preventDefault();
    const image = imageDialog.querySelector('[data-lightbox-image]');
    image.src = link.href;
    image.alt = link.dataset.caption || link.querySelector('img')?.alt || 'Изображение';
    imageDialog.querySelector('#image-caption').textContent = image.alt;
    openDialog(imageDialog);
});
const confirmation = document.getElementById('confirm-dialog');
let pendingSubmission = null;
const approvedForms = new WeakSet();
const loadingButtons = new Map();
document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement)) return;
    if (form.dataset.confirm && confirmation && !approvedForms.has(form)) {
        event.preventDefault();
        pendingSubmission = { form, button: event.submitter };
        confirmation.querySelector('[data-confirm-message]').textContent = form.dataset.confirm;
        openDialog(confirmation);
        return;
    }
    approvedForms.delete(form);
    if (form.matches('[data-submit-form]') || form.dataset.confirm) {
        const button = event.submitter || form.querySelector('button[type=submit], button:not([type])');
        if (button) {
            loadingButtons.set(button, [...button.childNodes].map((node) => node.cloneNode(true)));
            button.replaceChildren(document.createTextNode(button.dataset.loadingText || 'Подождите…'));
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');
        }
    }
});
confirmation?.querySelector('[data-confirm-accept]')?.addEventListener('click', () => {
    if (!pendingSubmission) return;
    const { form, button } = pendingSubmission;
    approvedForms.add(form);
    closeDialog(confirmation);
    pendingSubmission = null;
    if (button instanceof HTMLButtonElement) form.requestSubmit(button);
    else form.requestSubmit();
});
addEventListener('pageshow', () => {
    loadingButtons.forEach((nodes, button) => { button.replaceChildren(...nodes); button.disabled = false; button.removeAttribute('aria-busy'); });
    loadingButtons.clear();
    closeMenu();
    scheduleScroll();
});
document.querySelectorAll('[data-character-count]').forEach((input) => {
    const target = document.querySelector('[data-count-for="' + input.id + '"]');
    const update = () => { if (target) target.textContent = [...input.value].length + '/' + input.maxLength; };
    input.addEventListener('input', update);
    update();
});
const wizard = document.querySelector('[data-wizard]');
if (wizard) {
    const steps = [...wizard.querySelectorAll('[data-step]')];
    const stepButtons = [...wizard.querySelectorAll('[data-step-go]')];
    let active = 0;
    function review() {
        const device = wizard.querySelector('[name=device]');
        const service = wizard.querySelector('[name=service]:checked');
        const master = wizard.querySelector('[name=master_id]:checked');
        wizard.querySelector('[data-review-device]').textContent = device.value || 'Укажите модель';
        wizard.querySelector('[data-review-service]').textContent = service?.dataset.serviceLabel || 'Не выбрано';
        wizard.querySelector('[data-review-master]').textContent = master?.dataset.masterLabel || 'Назначит модератор';
    }
    function filterMasters() {
        const service = wizard.querySelector('[name=service]:checked')?.value;
        wizard.querySelectorAll('[data-master-option]').forEach((option) => {
            let services = [];
            try { services = JSON.parse(option.dataset.services); } catch {}
            const input = option.querySelector('input');
            const eligible = !service || services.includes(service);
            option.hidden = !eligible;
            input.disabled = !eligible;
            if (!eligible && input.checked) wizard.querySelector('[name=master_id][value=""]').checked = true;
        });
        review();
    }
    function validateStep(index) {
        const controls = [...steps[index].querySelectorAll('input, select, textarea')];
        const invalid = controls.find((input) => input.willValidate && !input.checkValidity());
        if (invalid) { invalid.reportValidity(); return false; }
        return true;
    }
    function showStep(index, focus = true) {
        active = Math.max(0, Math.min(steps.length - 1, index));
        steps.forEach((step, i) => { step.classList.toggle('is-current', i === active); step.setAttribute('aria-hidden', String(i !== active)); });
        stepButtons.forEach((button, i) => {
            if (i === active) button.setAttribute('aria-current', 'step'); else button.removeAttribute('aria-current');
            button.classList.toggle('is-complete', i < active);
        });
        wizard.querySelector('.wizard-progress i').style.transform = 'scaleX(' + (active + 1) / steps.length + ')';
        review();
        if (focus) {
            const heading = steps[active].querySelector('h2');
            heading.setAttribute('tabindex', '-1');
            heading.focus({ preventScroll: true });
            wizard.scrollIntoView({ block: 'start', behavior: motionAllowed() ? 'smooth' : 'instant' });
        }
    }
    wizard.querySelectorAll('[data-next-step]').forEach((button) => button.addEventListener('click', () => { if (validateStep(active)) showStep(active + 1); }));
    wizard.querySelectorAll('[data-prev-step]').forEach((button) => button.addEventListener('click', () => showStep(active - 1)));
    stepButtons.forEach((button) => button.addEventListener('click', () => {
        const target = Number(button.dataset.stepGo);
        if (target > active) for (let i = active; i < target; i++) if (!validateStep(i)) return;
        showStep(target);
    }));
    wizard.addEventListener('change', (event) => { if (event.target.name === 'service') filterMasters(); review(); });
    wizard.addEventListener('input', review);
    let invalidScheduled = false;
    wizard.addEventListener('invalid', (event) => {
        if (invalidScheduled) return;
        invalidScheduled = true;
        const index = steps.findIndex((step) => step.contains(event.target));
        if (index >= 0 && index !== active) showStep(index, false);
        setTimeout(() => { invalidScheduled = false; }, 0);
    }, true);
    filterMasters();
    wizard.classList.add('wizard-ready');
    let serverErrors = [];
    try { serverErrors = JSON.parse(wizard.dataset.errorFields || '[]'); } catch {}
    const firstError = [...wizard.elements].find((input) => serverErrors.includes(input.name?.replace(/\[\]$/, '')));
    const errorStep = firstError ? steps.findIndex((step) => step.contains(firstError)) : 0;
    showStep(Math.max(0, errorStep), false);
}
const photoInput = document.querySelector('[data-photo-input]');
if (photoInput) {
    const holder = document.querySelector('[data-photo-preview]');
    const error = document.querySelector('[data-photo-error]');
    let urls = [];
    function updatePhotos() {
        urls.forEach((url) => URL.revokeObjectURL(url)); urls = [];
        holder.replaceChildren();
        const files = [...photoInput.files];
        const invalid = files.find((file) => !['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 4 * 1024 * 1024);
        const message = files.length > 3 ? 'Выберите не более трёх фотографий.' : invalid ? 'Нужны JPG, PNG или WebP не больше 4 МБ каждый.' : '';
        photoInput.setCustomValidity(message);
        error.textContent = message;
        files.slice(0, 5).forEach((file, index) => {
            const box = document.createElement('div'); box.className = 'photo-preview';
            if (['image/jpeg', 'image/png', 'image/webp'].includes(file.type)) {
                const image = document.createElement('img'); const url = URL.createObjectURL(file); urls.push(url); image.src = url; image.alt = 'Выбранная фотография ' + (index + 1); box.append(image);
            }
            const name = document.createElement('small'); name.textContent = file.name; box.append(name);
            const button = document.createElement('button'); button.type = 'button'; button.textContent = '×'; button.setAttribute('aria-label', 'Убрать фотографию ' + (index + 1));
            button.addEventListener('click', () => {
                if (typeof DataTransfer === 'function') {
                    const data = new DataTransfer(); files.forEach((item, i) => { if (i !== index) data.items.add(item); }); photoInput.files = data.files;
                } else photoInput.value = '';
                updatePhotos();
            });
            box.append(button); holder.append(box);
        });
    }
    photoInput.addEventListener('change', updatePhotos);
}
const coverInput = document.querySelector('[data-cover-input]');
if (coverInput) {
    const preview = document.querySelector('[data-cover-preview]');
    const previous = preview.src;
    let url;
    coverInput.addEventListener('change', () => {
        if (url) URL.revokeObjectURL(url);
        const file = coverInput.files[0];
        const invalid = file && (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 3 * 1024 * 1024);
        coverInput.setCustomValidity(invalid ? 'Выберите JPG, PNG или WebP не больше 3 МБ.' : '');
        if (!file || invalid) { preview.src = previous; if (invalid) coverInput.reportValidity(); return; }
        url = URL.createObjectURL(file); preview.src = url;
    });
}
const bulkItems = [...document.querySelectorAll('[data-bulk-item]')];
const selectAll = document.querySelector('[data-select-all]');
const bulkSubmit = document.querySelector('[data-bulk-submit]');
function updateBulk() {
    const count = bulkItems.filter((input) => input.checked).length;
    if (bulkSubmit) bulkSubmit.disabled = count === 0;
    if (selectAll) { selectAll.checked = count > 0 && count === bulkItems.length; selectAll.indeterminate = count > 0 && count < bulkItems.length; }
}
selectAll?.addEventListener('change', () => { bulkItems.forEach((input) => { input.checked = selectAll.checked; }); updateBulk(); });
bulkItems.forEach((input) => input.addEventListener('change', updateBulk));
updateBulk();
const errors = document.querySelector('[data-error-summary]');
if (errors) {
    errors.focus({ preventScroll: true });
    errors.scrollIntoView({ block: 'center', behavior: 'instant' });
}
