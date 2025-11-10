// booking.js

document.addEventListener('DOMContentLoaded', function () {
    const bookingSection = document.getElementById('booking_page');
    if (!bookingSection) return; // exit if not on booking page

    const checkboxes = bookingSection.querySelectorAll('.service-checkbox');
    const summaryList = bookingSection.querySelector('#summary-service-list');
    const totalCostEl = bookingSection.querySelector('#total-cost');
    const dateInput = bookingSection.querySelector('#date');
    const timeInput = bookingSection.querySelector('#time');
    const summaryDate = bookingSection.querySelector('#summary-date');
    const summaryTime = bookingSection.querySelector('#summary-time');

    let selectedServices = [];

    checkboxes.forEach(box => {
        box.addEventListener('change', () => {
            const name = box.dataset.name;
            const price = parseFloat(box.dataset.price);

            if (box.checked) {
                selectedServices.push({ name, price });
            } else {
                selectedServices = selectedServices.filter(s => s.name !== name);
            }

            updateSummary();
        });
    });

    function updateSummary() {
        summaryList.innerHTML = '';
        let total = 0;

        selectedServices.forEach(service => {
            total += service.price;
            const li = document.createElement('li');
            li.className = 'list-group-item d-flex justify-content-between';
            li.innerHTML = `<span>${service.name}</span><span>£${service.price.toFixed(2)}</span>`;
            summaryList.appendChild(li);
        });

        totalCostEl.textContent = total.toFixed(2);
    }

    dateInput?.addEventListener('change', () => summaryDate.textContent = dateInput.value || '—');
    timeInput?.addEventListener('change', () => summaryTime.textContent = timeInput.value || '—');
});
