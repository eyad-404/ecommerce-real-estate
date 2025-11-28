// Demo property data
const properties = [
    { id: 1, title: "Modern Apartment", type: "Apartment", location: "Maadi", size: 120, price: 1500000, image: "https://via.placeholder.com/400x200" },
    { id: 2, title: "Luxury Villa", type: "Villa", location: "New Cairo", size: 350, price: 4500000, image: "https://via.placeholder.com/400x200" },
    { id: 3, title: "Cozy Studio", type: "Studio", location: "Nasr City", size: 60, price: 700000, image: "https://via.placeholder.com/400x200" },
    { id: 4, title: "Penthouse Suite", type: "Apartment", location: "Maadi", size: 200, price: 3000000, image: "https://via.placeholder.com/400x200" },
];

// Select DOM elements
const container = document.getElementById('propertiesContainer');
const form = document.getElementById('filterForm');
const resetBtn = document.getElementById('resetBtn');

function renderProperties(filteredProps) {
    container.innerHTML = '';
    if (filteredProps.length === 0) {
        container.innerHTML = '<div class="col-12"><p class="text-center text-muted fs-5">No properties found matching your criteria.</p></div>';
        return;
    }

    filteredProps.forEach(prop => {
        const col = document.createElement('div');
        col.className = 'col';
        col.innerHTML = `
        <div class="card bg-dark text-white shadow h-100">
            <img src="${prop.image}" class="card-img-top object-fit-cover" alt="${prop.title}" style="height:200px;">
            <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h5 class="card-title text-white mb-0">${prop.title}</h5>
                    <span class="badge bg-secondary">ID: ${prop.id}</span>
                </div>
                <p class="card-text mb-1"><span class="text-info fw-bold">Type:</span> ${prop.type}</p>
                <p class="card-text mb-1"><span class="text-info fw-bold">Size:</span> ${prop.size} sqm</p>
                <p class="card-text mb-1"><span class="text-info fw-bold">Location:</span> ${prop.location}</p>
                <p class="card-text mb-3"><span class="text-success fw-bold">Price:</span> ${prop.price.toLocaleString()} EGP</p>
                <a href="#" class="btn btn-success mt-auto">View Details</a>
            </div>
        </div>`;
        container.appendChild(col);
    });
}

function applyFilters() {
    let filtered = [...properties];

    const search = document.getElementById('search_term').value.toLowerCase();
    const type = document.getElementById('property_type').value;
    const location = document.getElementById('property_location').value;
    const minPrice = parseFloat(document.getElementById('min_price').value);
    const maxPrice = parseFloat(document.getElementById('max_price').value);
    const sort = document.getElementById('sort_by').value;

    // Search filter
    if (search) {
        filtered = filtered.filter(p => 
            p.title.toLowerCase().includes(search) ||
            p.location.toLowerCase().includes(search) ||
            p.id.toString() === search
        );
    }

    // Type filter
    if (type) filtered = filtered.filter(p => p.type === type);

    // Location filter
    if (location) filtered = filtered.filter(p => p.location === location);

    // Min price filter
    if (!isNaN(minPrice)) filtered = filtered.filter(p => p.price >= minPrice);

    // Max price filter
    if (!isNaN(maxPrice)) filtered = filtered.filter(p => p.price <= maxPrice);

    // Sorting
    switch(sort) {
        case 'price_asc': filtered.sort((a,b)=>a.price-b.price); break;
        case 'price_desc': filtered.sort((a,b)=>b.price-a.price); break;
        case 'size_desc': filtered.sort((a,b)=>b.size-a.size); break;
        case 'id_desc':
        default: filtered.sort((a,b)=>b.id-a.id); break;
    }

    renderProperties(filtered);
}

// Event listeners
form.addEventListener('submit', e => {
    e.preventDefault();
    applyFilters();
});

resetBtn.addEventListener('click', () => {
    form.reset();
    renderProperties(properties);
});

// Initial render
renderProperties(properties);
