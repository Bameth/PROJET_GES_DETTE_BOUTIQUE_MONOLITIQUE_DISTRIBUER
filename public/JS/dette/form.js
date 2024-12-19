// Charger les données dynamiquement
document.addEventListener('DOMContentLoaded', () => {
    // Charger les articles
    fetch('/api/articles')
        .then((response) => response.json())
        .then((data) => {
            const articleSelect = document.getElementById('article');
            data.articles.forEach((article) => {
                const option = document.createElement('option');
                option.value = article.id;
                option.textContent = `${article.nom} - ${article.prix} FCFA`;
                articleSelect.appendChild(option);
            });
        });

    // Charger les clients
    fetch('/api/clients')
        .then((response) => response.json())
        .then((data) => {
            const clientSelect = document.getElementById('client');
            data.clients.forEach((client) => {
                const option = document.createElement('option');
                option.value = client.id;
                option.textContent = `${client.surname} (${client.telephone})`;
                clientSelect.appendChild(option);
            });
        });
    updateCart();
});

// Ajouter un article au panier
document.getElementById('add-to-cart').addEventListener('click', () => {
    const articleId = document.getElementById('article').value;
    const quantity = document.getElementById('quantity').value;

    fetch('/api/panier/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ article: articleId, quantity: parseInt(quantity, 10) }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                updateCart();
            }
        });

});

// Sauvegarder la dette
document.getElementById('save-debt').addEventListener('click', () => {
    const clientId = document.getElementById('client').value;

    fetch('/api/dette/save', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ client: clientId }),
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                // Réinitialiser le formulaire
                updateCart();
            }
        });
});

// Mettre à jour le panier
function updateCart() {
    fetch('/api/panier')
        .then((response) => response.json())
        .then((data) => {
            const cartTableBody = document.querySelector('#cart-table tbody');
            const totalDisplay = document.getElementById('montant-total');

            cartTableBody.innerHTML = '';
            let total = 0;
            data.panier.forEach((item) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td class="py-3 px-4 text-center">${item.article.nom}</td>
                    <td class="py-3 px-4 text-center">${item.article.prix} FCFA</td>
                    <td class="py-3 px-4 text-center">${item.qte}</td>
                    <td class="py-3 px-4 text-center">${item.total} FCFA</td>
                `;
                cartTableBody.appendChild(row);
                total += item.total;
            });

            totalDisplay.textContent = total;
        });
}
//vider le panier les boutons
document.getElementById('clear-cart').addEventListener('click', () => {
    fetch('/api/panier/remove', {
        method: 'POST',
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                // Rafraîchir la liste du panier
                updateCart();
            }
        });
});
