document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('business_name');
  const suggestionBox = document.getElementById('place-suggestions');
  let timeout;
  let currentIndex = -1;

  // Clear suggestions when clicking outside
  document.addEventListener('click', function(e) {
    if (!input.contains(e.target) && !suggestionBox.contains(e.target)) {
      suggestionBox.innerHTML = '';
      currentIndex = -1;
    }
  });

  input.addEventListener('input', function () {
    clearTimeout(timeout);
    const query = input.value.trim();
    currentIndex = -1;

    if (query.length < 3) {
      suggestionBox.innerHTML = '';
      return;
    }

    // Show loading state
    suggestionBox.innerHTML = '<div class="list-group-item">🔄 Searching...</div>';

    timeout = setTimeout(() => {
      fetch(`/v1/place-autocomplete?query=${encodeURIComponent(query)}`)
        .then(response => {
          if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
          }
          return response.json();
        })
        .then(data => {
          console.log('API Response:', data); // Debug log
          suggestionBox.innerHTML = '';

          if (data.status === 'error') {
            suggestionBox.innerHTML = `<div class="list-group-item text-danger">❌ ${data.message}</div>`;
            return;
          }

          const suggestions = data.suggestions || [];

          if (suggestions.length === 0) {
            suggestionBox.innerHTML = '<div class="list-group-item text-muted">No suggestions found</div>';
            addManualOption(query);
          } else {
            renderSuggestions(suggestions, query);
          }
        })
        .catch(error => {
          console.error('Fetch error:', error);
          suggestionBox.innerHTML = '<div class="list-group-item text-danger">❌ Error fetching suggestions</div>';
        });
    }, 500);
  });

  function renderSuggestions(suggestions, originalQuery) {
    const items = [];

    // Limit to 5 suggestions
    suggestions.slice(0, 5).forEach((wrapper, index) => {
      const prediction = wrapper.placePrediction;

      if (!prediction) {
        console.warn('No placePrediction found in wrapper:', wrapper);
        return;
      }

      const fullText = prediction?.text?.text || 'Unknown place';
      const mainText = prediction?.structuredFormat?.mainText?.text || fullText;
      const secondaryText = prediction?.structuredFormat?.secondaryText?.text || '';
      const placeId = prediction?.placeId || '';

      const item = document.createElement('a');
      item.classList.add('list-group-item', 'list-group-item-action');

      // Build the display text
      let displayHtml = `<strong>${escapeHtml(mainText)}</strong>`;
      if (secondaryText) {
        displayHtml += `<br><small class="text-muted">${escapeHtml(secondaryText)}</small>`;
      }

      item.innerHTML = displayHtml;
      item.href = 'javascript:void(0)';
      item.setAttribute('data-index', index);
      item.setAttribute('data-place-id', placeId);
      item.setAttribute('data-main-text', mainText);
      item.setAttribute('data-full-text', fullText);

      item.addEventListener('click', function () {
        selectPlace(mainText, placeId);
      });

      suggestionBox.appendChild(item);
      items.push(item);
    });

    // Add manual option
    addManualOption(originalQuery);
  }

  function addManualOption(originalQuery) {
    const manualItem = document.createElement('a');
    manualItem.classList.add('list-group-item', 'list-group-item-action', 'text-muted');
    manualItem.href = 'javascript:void(0)';
    manualItem.innerHTML = `➕ Add manually: <strong>${escapeHtml(originalQuery)}</strong>`;
    manualItem.addEventListener('click', function () {
        selectPlace(originalQuery, '');
    });

    suggestionBox.appendChild(manualItem);
  }


  function selectPlace(placeName, placeId) {
    input.value = placeName;
    suggestionBox.innerHTML = '';
    currentIndex = -1;

    // Store place ID in hidden input if it exists
    const hiddenInput = document.getElementById('place_id');
    if (hiddenInput) {
      hiddenInput.value = placeId || '';
    }

    // Trigger change event for any listeners
    input.dispatchEvent(new Event('change', { bubbles: true }));

    console.log('Selected place:', { name: placeName, id: placeId });
  }

  input.addEventListener('keydown', function (e) {
    const items = suggestionBox.querySelectorAll('.list-group-item-action');

    if (items.length === 0) return;

    if (e.key === 'ArrowDown') {
      e.preventDefault();
      currentIndex = (currentIndex + 1) % items.length;
      highlightItem(items);
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      currentIndex = (currentIndex - 1 + items.length) % items.length;
      highlightItem(items);
    } else if (e.key === 'Enter') {
      e.preventDefault();
      if (currentIndex >= 0 && items[currentIndex]) {
        items[currentIndex].click();
      }
    } else if (e.key === 'Escape') {
      suggestionBox.innerHTML = '';
      currentIndex = -1;
    }
  });

  function highlightItem(items) {
    items.forEach(item => item.classList.remove('active'));
    if (currentIndex >= 0 && items[currentIndex]) {
      items[currentIndex].classList.add('active');
      // Scroll into view if needed
      items[currentIndex].scrollIntoView({
        behavior: 'smooth',
        block: 'nearest'
      });
    }
  }

  // Utility function to escape HTML
  function escapeHtml(text) {
    const map = {
      '&': '&amp;',
      '<': '&lt;',
      '>': '&gt;',
      '"': '&quot;',
      "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
  }
});