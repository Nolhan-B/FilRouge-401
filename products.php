<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <link rel="stylesheet" href="css/products.css">
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/footer.css">
    
    <link href="https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap" rel="stylesheet">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0">
    <title>CritiClick - Fil rouge</title>
</head>
<body>

<?php include 'components/navbar.php'; ?>

<main>
    <section class="filter">
        <div class="filter-container">
            <div class="filter-Search-container">
                <label for="categoryFilter">Filtrer par catégorie :</label>
                <select class="categoryFilter" id="categoryFilter">
                    <option value="all">Toutes</option>
                </select>
            </div>
            <div class="filter-Search-container">
                <label for="searchBar">Recherche :</label>
                <input class="productsFilter" type="text" id="searchBar" placeholder="Rechercher un produit...">
            </div>
        </div>
    </section>

    <section class="products">
        <h1>Les fiches produits</h1>
        <div class="products-container" id="products-container">
        </div>
        <p id="no-products-message" style="display:none;">Aucune fiche d'article ne correspond à votre recherche.</p>
    </section>
</main>

<?php include 'components/footer.php'; ?>

<script>
function sanitizeURL(url) {
    const parser = document.createElement('a');
    parser.href = url;
    return parser.protocol === "http:" || parser.protocol === "https:" ? url : "";
}

async function fetchCategories() {
    try {
        const response = await fetch('fetch/fetchPublishedCategory.php');
        if (response.ok) {
            return await response.json();
        } else {
            console.error('Erreur lors de la récupération des catégories');
            return [];
        }
    } catch (error) {
        console.error('Erreur réseau:', error);
        return [];
    }
}

async function fetchProducts() {
    try {
        const response = await fetch('fetch/fetchPublishedProducts.php');
        if (response.ok) {
            return await response.json();
        } else {
            console.error('Erreur lors de la récupération des produits');
            return [];
        }
    } catch (error) {
        console.error('Erreur réseau:', error);
        return [];
    }
}

function displayCategories(categories) {
    const categoryFilter = document.getElementById('categoryFilter');
    categories.forEach(categorie => {
        const option = document.createElement('option');
        option.value = categorie.filtreTag.toLowerCase();
        option.textContent = categorie.nom;
        categoryFilter.appendChild(option);
    });
}

function displayProducts(produits, categories) {
    const productsContainer = document.getElementById('products-container');
    productsContainer.innerHTML = '';

    let hasProducts = false;

    produits.forEach(produit => {
        const categorieProduit = categories.find(cat => cat.id === produit.categorie_id);
        const nomCategorie = categorieProduit ? categorieProduit.nom : "Catégorie inconnue";
        const filtreTagCategorie = categorieProduit ? categorieProduit.filtreTag.toLowerCase() : "inconnu";

        const productDiv = document.createElement('div');
        productDiv.classList.add('products-item');
        productDiv.setAttribute('data-category', filtreTagCategorie);
        productDiv.setAttribute('data-name', produit.nom.toLowerCase());

        const productCategory = document.createElement('p');
        productCategory.classList.add('products-item-category');
        productCategory.style.backgroundColor = "white";
        productCategory.textContent = nomCategorie;

        const productName = document.createElement('p');
        productName.classList.add('products-item-name');
        productName.textContent = produit.nom;

        const productImage = document.createElement('img');
        productImage.classList.add('products-item-picture');
        productImage.src = sanitizeURL(produit.image);
        productImage.alt = 'Photo du produit';

        const productDescription = document.createElement('p');
        productDescription.classList.add('products-item-text');
        productDescription.textContent = produit.description;

        const productLink = document.createElement('a');
        productLink.href = `ProductDetails.php?id=${produit.id}`;
        productLink.target = '_self';

        const productButton = document.createElement('button');
        productButton.classList.add('products-item-button');
        productButton.textContent = 'Voir la fiche';

        productLink.appendChild(productButton);
        productDiv.appendChild(productCategory);
        productDiv.appendChild(productName);
        productDiv.appendChild(productImage);
        productDiv.appendChild(productDescription);
        productDiv.appendChild(productLink);

        productsContainer.appendChild(productDiv);
        hasProducts = true;
    });

    document.getElementById('no-products-message').style.display = hasProducts ? 'none' : 'flex';
};

function filterProducts() {
    const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
    const searchQuery = document.getElementById('searchBar').value.toLowerCase();
    const products = document.querySelectorAll('.products-item');

    let visibleProductCount = 0;

    products.forEach(product => {
        const productCategory = product.getAttribute('data-category');
        const productName = product.getAttribute('data-name');

        if ((categoryFilter === 'all' || productCategory.includes(categoryFilter)) && productName.includes(searchQuery)) {
            product.style.display = 'flex';
            visibleProductCount++;
        } else {
            product.style.display = 'none';
        }
    });

    document.getElementById('no-products-message').style.display = visibleProductCount === 0 ? 'flex' : 'none';
}

function updateUrlParams() {
    const categoryFilter = document.getElementById('categoryFilter').value;
    const searchQuery = document.getElementById('searchBar').value;

    const url = new URL(window.location);
    url.searchParams.set('category', categoryFilter);
    url.searchParams.set('search', searchQuery);

    window.history.replaceState(null, null, url);
}

document.getElementById('categoryFilter').addEventListener('change', () => {
    filterProducts();
    updateUrlParams();
});
document.getElementById('searchBar').addEventListener('input', () => {
    filterProducts();
    updateUrlParams();
});

window.addEventListener('load', async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const categoryFromUrl = urlParams.get('category');
    const searchFromUrl = urlParams.get('search');

    const categories = await fetchCategories();
    displayCategories(categories);

    if (categoryFromUrl) {
        document.getElementById('categoryFilter').value = categoryFromUrl;
    }

    if (searchFromUrl) {
        document.getElementById('searchBar').value = searchFromUrl;
    }

    const produits = await fetchProducts();
    displayProducts(produits, categories);
    filterProducts();
});
</script>

</body>
</html>