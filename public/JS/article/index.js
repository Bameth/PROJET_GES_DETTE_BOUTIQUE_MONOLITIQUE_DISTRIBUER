let currentPage = 1;
const urlParams = new URLSearchParams(window.location.search);
const pageFromUrl = urlParams.get('page');
const page = pageFromUrl ? parseInt(pageFromUrl, 10) : 1;

displayArticles(page);

function displayArticles(page = 1) {
    currentPage = page;
    const list = document.getElementById("articles-list");

    // Mettre à jour l'URL sans recharger la page
    history.pushState(null, '', `?page=${encodeURIComponent(page)}`);

    // Animation de chargement
    const loadingMessage = document.createElement("tr");
    loadingMessage.classList.add("animate-pulse");
    loadingMessage.innerHTML = `
        <td colspan="7" class="text-center py-4 text-gray-500">
            <div class="flex items-center justify-center space-x-2">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Chargement des articles...</span>
            </div>
        </td>
    `;
    list.appendChild(loadingMessage);

    fetch(`/api/article?page=${page}`)
    .then((response) => response.json())
    .then((responseData) => {
        console.log("Données récupérées :", responseData);
        const data = responseData.articles;
        list.innerHTML = "";

        data.forEach((item) => {
            const row = document.createElement("tr");
            row.classList.add("border-b", "hover:bg-gray-50");

            row.innerHTML = `
                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.id}</td>
                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.nom}</td>
                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.prix}</td>
                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.qte}</td>
                <td class="px-6 py-4 flex justify-center space-x-4 text-center">
                    <a href="/articles/show?article_id=${item.id}" class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="#" class="text-yellow-500 hover:text-yellow-700 transition transform hover:scale-110">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="#" class="text-red-600 hover:text-red-800 transition transform hover:scale-110" onclick="toggleDeleteModal()">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            `;
            list.appendChild(row);
        });

        updatePagination(responseData.page, responseData.hasMore);
        console.log(responseData); // Pour déboguer les données reçues
    })
    .catch((error) => {
        console.error("Erreur lors de la récupération des données :", error);
        list.innerHTML = `<tr>
            <td colspan="7" class="text-center py-4 text-red-500">
                <i class="fas fa-exclamation-triangle"></i> Erreur de chargement des articles.
            </td>
        </tr>`;
    });
}

function updatePagination(currentPage, hasMore) {
    const prevButton = document.getElementById("prev");
    const nextButton = document.getElementById("next");
    const currentButton = document.getElementById("current");

    // Mettre à jour le texte du bouton actuel
    currentButton.innerText = currentPage;

    // Activer/désactiver les boutons "Précédent" et "Suivant"
    prevButton.disabled = currentPage === 1;
    nextButton.disabled = !hasMore;

    prevButton.onclick = () => {
        if (currentPage > 1) {
            displayArticles(currentPage - 1);
        }
    };

    nextButton.onclick = () => {
        if (hasMore) {
            displayArticles(currentPage + 1);
        }
    };
}

// Initialiser l'affichage des articles pour la première page
displayArticles(currentPage);
