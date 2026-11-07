document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('booking_page');
    if (!root) return;
    const form = document.getElementById('appointmentForm');
    const boxes = [...root.querySelectorAll('.service-checkbox')];
    const search = document.getElementById('service-search');
    const list = document.getElementById('summary-service-list');
    const empty = document.getElementById('booking-summary-empty');
    const date = document.getElementById('appointment_date');
    const time = document.getElementById('appointment_time');
    const filters = [...root.querySelectorAll('[data-booking-filter]')];
    let filter = 'all';
    const money = new Intl.NumberFormat('en-GB', {style: 'currency', currency: 'GBP'});
    function duration(minutes) {
        const hours = Math.floor(minutes / 60), rest = minutes % 60;
        return minutes ? [hours ? hours + (hours === 1 ? ' hr' : ' hrs') : '', rest ? rest + ' min' : ''].filter(Boolean).join(' ') : 'To be confirmed';
    }
    function applyFilter() {
        const query = (search?.value || '').trim().toLocaleLowerCase();
        let visible = 0;
        boxes.forEach(function (box) {
            const card = box.closest('[data-service-card]');
            const matching = box.dataset.name.toLocaleLowerCase().includes(query) && (filter !== 'selected' || box.checked);
            card.hidden = !matching;
            if (matching) visible++;
        });
        const noResults = root.querySelector('.booking-no-results');
        if (noResults) noResults.hidden = visible > 0;
        const results = document.getElementById('booking-results');
        if (results) results.textContent = visible + (visible === 1 ? ' service' : ' services') + (filter === 'selected' ? ' selected' : ' to choose from');
    }
    function updateSummary() {
        const selected = boxes.filter(box => box.checked);
        let total = 0, minutes = 0;
        list.replaceChildren();
        boxes.forEach(function (box) {
            box.closest('[data-service-card]').classList.toggle('is-selected', box.checked);
            box.closest('label').querySelector('.booking-choice-label').textContent = box.checked ? 'Selected' : 'Select service';
        });
        selected.forEach(function (box) {
            const quantity = Number(box.dataset.quantity) || 1;
            const lineTotal = Number(box.dataset.price) * quantity;
            const lineMinutes = Number(box.dataset.minutes) * quantity;
            total += lineTotal;
            minutes += lineMinutes;
            const row = document.createElement('li');
            const info = document.createElement('div');
            const name = document.createElement('strong');
            const meta = document.createElement('small');
            const price = document.createElement('span');
            const remove = document.createElement('button');
            name.textContent = box.dataset.name;
            meta.textContent = duration(lineMinutes) + (quantity > 1 ? ' · quantity ' + quantity : '');
            price.textContent = money.format(lineTotal);
            remove.type = 'button'; remove.className = 'booking-summary-remove'; remove.textContent = 'Remove';
            remove.setAttribute('aria-label', 'Remove ' + box.dataset.name);
            remove.addEventListener('click', function () { box.checked = false; updateSummary(); applyFilter(); });
            info.append(name, meta, remove); row.append(info, price); list.append(row);
        });
        empty.hidden = selected.length > 0;
        document.getElementById('total-cost').textContent = money.format(total);
        document.getElementById('booking-mobile-total').textContent = money.format(total);
        document.getElementById('booking-mobile-count').textContent = selected.length ? selected.length + (selected.length === 1 ? ' service selected' : ' services selected') : 'Choose a service';
        document.getElementById('booking-filter-count')?.replaceChildren(document.createTextNode(String(selected.length)));
        document.getElementById('booking-duration').textContent = selected.length ? duration(minutes) : '—';
        root.querySelectorAll('.booking-continue').forEach(button => { button.disabled = selected.length === 0; });
        document.getElementById('booking-step-services').classList.toggle('is-complete', selected.length > 0);
        document.getElementById('booking-step-time').classList.toggle('is-current', selected.length > 0);
        const serviceError = document.getElementById('booking-services-error');
        if (selected.length && serviceError) serviceError.hidden = true;
    }
    function updateVisit() {
        const target = document.getElementById('booking-visit');
        if (date.value && time.value) {
            const parts = date.value.split('-').map(Number);
            const label = new Intl.DateTimeFormat('en-GB', {weekday: 'short', day: 'numeric', month: 'short', timeZone: 'UTC'}).format(new Date(Date.UTC(parts[0], parts[1] - 1, parts[2])));
            target.textContent = label + ', ' + time.value + ' (UK)';
        } else target.textContent = 'Choose a date & time';
        document.getElementById('booking-step-time').classList.toggle('is-complete', !!date.value && !!time.value);
        const error = document.getElementById('booking-appointment-error');
        if (error && date.value && time.value) error.hidden = true;
    }
    boxes.forEach(box => box.addEventListener('change', function () { updateSummary(); applyFilter(); }));
    search?.addEventListener('input', applyFilter);
    search?.addEventListener('keydown', function (event) { if (event.key === 'Enter') event.preventDefault(); });
    filters.forEach(function (button) {
        button.addEventListener('click', function () {
            filter = button.dataset.bookingFilter;
            filters.forEach(item => { const active = item === button; item.classList.toggle('is-active', active); item.setAttribute('aria-pressed', String(active)); });
            applyFilter();
        });
    });
    document.getElementById('booking-clear-search')?.addEventListener('click', function () {
        search.value = ''; filters.find(item => item.dataset.bookingFilter === 'all').click(); search.focus();
    });
    date.addEventListener('change', updateVisit); time.addEventListener('change', updateVisit);
    form.addEventListener('submit', function (event) {
        if (!boxes.some(box => box.checked)) {
            event.preventDefault();
            const error = document.getElementById('booking-services-error');
            if (error) error.hidden = false;
            if (boxes.length) { filter = 'all'; search.value = ''; applyFilter(); boxes[0].focus(); }
            return;
        }
        if (!form.checkValidity()) return;
        root.querySelectorAll('.booking-continue').forEach(button => { button.disabled = true; button.textContent = 'Saving your selection…'; });
    });
    // Returning with the browser Back button should not leave a disabled submit control.
    window.addEventListener('pageshow', function () {
        root.querySelectorAll('.booking-continue').forEach(button => { button.textContent = button.closest('.booking-mobile-action') ? 'Continue →' : 'Continue to checkout →'; });
        updateSummary(); updateVisit();
    });
    updateSummary(); updateVisit(); applyFilter();
});
