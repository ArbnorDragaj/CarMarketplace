document.addEventListener('DOMContentLoaded', function () {
  function updateCounts(counts) {
    if (!counts) {
      return;
    }

    var totalCars = document.getElementById('totalCars');
    var activeCars = document.getElementById('activeCars');
    var inactiveCars = document.getElementById('inactiveCars');

    if (totalCars) {
      totalCars.textContent = counts.total;
    }

    if (activeCars) {
      activeCars.textContent = counts.active;
    }

    if (inactiveCars) {
      inactiveCars.textContent = counts.inactive;
    }
  }

  function showAdminMessage(message, type) {
    var container = document.querySelector('.admin-cars-container');
    var stats = document.querySelector('.admin-stats');

    if (!container || !stats) {
      return;
    }

    var messageBox = document.querySelector('.ajax-admin-message');

    if (!messageBox) {
      messageBox = document.createElement('div');
      container.insertBefore(messageBox, stats);
    }

    messageBox.className = 'admin-message ajax-admin-message ' + (type === 'error' ? 'error-message' : 'success-message');
    messageBox.textContent = message;
  }

  async function postForm(endpoint, form) {
    var response = await fetch(endpoint, {
      method: 'POST',
      body: new FormData(form),
      headers: {
        Accept: 'application/json'
      },
      credentials: 'same-origin'
    });

    var data = await response.json().catch(function () {
      return null;
    });

    if (!data || !data.success) {
      throw new Error((data && data.message) || 'Veprimi nuk u krye.');
    }

    return data;
  }

  document.querySelectorAll('.toggle-car-form').forEach(function (form) {
    form.addEventListener('submit', async function (event) {
      event.preventDefault();

      var button = form.querySelector('.toggle-status-btn');

      if (!button) {
        return;
      }

      var oldText = button.textContent;
      button.disabled = true;
      button.textContent = 'Duke ruajtur...';

      try {
        var data = await postForm('ajax/toggle_car_status.php', form);
        var isActive = data.status === 'active';
        var statusBadge = document.getElementById('status-' + data.id);

        if (statusBadge) {
          statusBadge.textContent = data.label;
          statusBadge.classList.toggle('active-status', isActive);
          statusBadge.classList.toggle('inactive-status', !isActive);
        }

        button.dataset.status = data.status;
        button.classList.toggle('deactivate-btn', isActive);
        button.classList.toggle('activate-btn', !isActive);
        button.textContent = isActive ? 'Caktivizo' : 'Aktivizo';

        updateCounts(data.counts);
        showAdminMessage(data.message, 'success');
      } catch (error) {
        button.textContent = oldText;
        showAdminMessage(error.message, 'error');
      } finally {
        button.disabled = false;
      }
    });
  });

  document.querySelectorAll('.delete-car-form').forEach(function (form) {
    form.addEventListener('submit', async function (event) {
      event.preventDefault();

      var message = form.dataset.confirm || 'A jeni i sigurt qe doni ta fshini kete veture?';

      if (!window.confirm(message)) {
        return;
      }

      var button = form.querySelector('.delete-car-btn');
      var oldText = button ? button.textContent : '';

      if (button) {
        button.disabled = true;
        button.textContent = 'Duke fshire...';
      }

      try {
        var data = await postForm('ajax/delete_car.php', form);
        var row = document.getElementById('car-row-' + data.id);

        if (row) {
          row.remove();
        }

        updateCounts(data.counts);
        showAdminMessage(data.message, 'success');

        var tbody = document.querySelector('.cars-table tbody');
        if (tbody && !tbody.querySelector('tr[id^="car-row-"]')) {
          var emptyRow = document.createElement('tr');
          emptyRow.innerHTML = '<td colspan="7">Nuk ka vetura te regjistruara.</td>';
          tbody.appendChild(emptyRow);
        }
      } catch (error) {
        if (button) {
          button.textContent = oldText;
          button.disabled = false;
        }

        showAdminMessage(error.message, 'error');
      }
    });
  });
});
