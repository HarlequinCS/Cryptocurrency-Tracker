// Fetch cryptocurrency data from Laravel proxy
async function fetchCryptoData() {
  try {
    const response = await fetch('/api/coins');
    if (!response.ok) throw new Error('Failed to fetch data');

    const data = await response.json();
    return data;
  } catch (error) {
    console.error('Error fetching cryptocurrency data:', error.message);
    const errorContainer = document.getElementById('errorContainer');
    if (errorContainer) {
      const errorAlert = document.createElement('div');
      errorAlert.classList.add('alert', 'alert-danger');
      errorAlert.textContent = `Failed to fetch cryptocurrency data. Error: ${error.message}. Please try again later.`;
      errorContainer.appendChild(errorAlert);
    }
    return [];
  }
}

// Create list item for each crypto
function createCryptoListItem(crypto) {
  const listItem = document.createElement('a');
  listItem.classList.add('list-group-item', 'list-group-item-action', 'd-flex', 'justify-content-between', 'align-items-center');

  const changeClass = crypto.price_change_percentage_24h >= 0 ? 'positive-change' : 'negative-change';

  listItem.innerHTML = `
    <div class="d-flex align-items-center">
        <img src="${crypto.image}" alt="${crypto.name}" class="crypto-icon mr-2">
        <div>
            <span class="font-weight-bold">${crypto.name} (${crypto.symbol.toUpperCase()})</span><br>
            <small>Rank: ${crypto.market_cap_rank} | Market Cap: $${crypto.market_cap.toLocaleString()}</small>
        </div>
    </div>
    <div class="text-right">
        <div>Price: $${crypto.current_price}</div>
        <div>24h Change: <span class="${changeClass}">${crypto.price_change_percentage_24h.toFixed(2)}%</span></div>
        <a href="/coins/${crypto.id}" class="btn btn-sm btn-primary mt-1">View Chart</a>
    </div>
  `;

  return listItem;
}

// Render crypto list
async function renderCryptoList(sortBy = 'rank') {
  const cryptoData = await fetchCryptoData();

  if (cryptoData.length > 0) {
    const filteredData = cryptoData.filter(c => c.market_cap_rank !== null);
    const sortedData = sortCryptoData(filteredData, sortBy);

    const cryptoListContainer = document.getElementById('cryptoList');
    cryptoListContainer.innerHTML = '';

    sortedData.forEach(crypto => {
      cryptoListContainer.appendChild(createCryptoListItem(crypto));
    });
  }
}

// Sort crypto data
function sortCryptoData(data, sortBy) {
  switch (sortBy) {
    case 'name': return data.sort((a, b) => a.name.localeCompare(b.name));
    case 'price': return data.sort((a, b) => b.current_price - a.current_price);
    case 'marketCap': return data.sort((a, b) => b.market_cap - a.market_cap);
    case 'change': return data.sort((a, b) => b.price_change_percentage_24h - a.price_change_percentage_24h);
    case 'volume': return data.sort((a, b) => b.total_volume - a.total_volume);
    case 'rank': return data.sort((a, b) => a.market_cap_rank - b.market_cap_rank);
    default: return data;
  }
}

// Handle sorting change
const sortSelect = document.getElementById('sortSelect');
if (sortSelect) {
  sortSelect.addEventListener('change', e => renderCryptoList(e.target.value));
}

// Load list on page load
window.onload = () => renderCryptoList();
