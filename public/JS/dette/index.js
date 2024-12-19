let currentPage = 1;
const urlParams = new URLSearchParams(window.location.search);
const pageFromUrl = urlParams.get('page');
const page = pageFromUrl ? parseInt(pageFromUrl, 10) : 1;

displayDettes(page);

function displayDettes(page = 1) {
    currentPage = page;
    const list = document.getElementById("dettes-list");

    // Mettre à jour l'URL sans recharger la page
    history.pushState(null, '', `?page=${page}`);

    // Animation de chargement
    const loadingMessage = document.createElement("tr");
    loadingMessage.classList.add("animate-pulse");
    loadingMessage.innerHTML = `
        <td colspan="7" class="text-center py-4 text-gray-500">
            <div class="flex items-center justify-center space-x-2">
                <i class="fas fa-spinner fa-spin"></i>
                <span>Chargement des dettes...</span>
            </div>
        </td>
    `;
    list.appendChild(loadingMessage);

    fetch(`/api/dette?page=${page}`)
        .then((response) => response.json())
        .then((responseData) => {
            const data = responseData.dettes;
            list.innerHTML = "";

            data.forEach((item) => {
                const row = document.createElement("tr");
                row.classList.add("border-b", "hover:bg-gray-50");

                row.innerHTML = `
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.id}</td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.montantTotal}</td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.montantRestant}</td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.montantVerse}</td>
                                <td class="px-6 py-4 text-center font-semibold text-gray-700">${item.statusDette}</td>
                                <td class="px-6 py-4 flex justify-center space-x-4 text-center">
                                    <a href="/dettes/show?dette_id=${item.id}" class="text-blue-600 hover:text-blue-800 transition transform hover:scale-110">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="#" class="text-yellow-500 hover:text-yellow-700 transition transform hover:scale-110">
                                        <i class="fas fa-credit-card"></i>
                                    </a>
                                </td>
                            `;
                list.appendChild(row);
            });

            updatePagination(responseData.page, responseData.hasMore);
        })
        .catch((error) => {
            console.error("Erreur lors de la récupération des données :", error);
            list.innerHTML = `<tr>
                <td colspan="7" class="text-center py-4 text-red-500">
                    <i class="fas fa-exclamation-triangle"></i> Erreur de chargement des Dettes.
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
            displayDettes(currentPage - 1);
        }
    };

    nextButton.onclick = () => {
        if (hasMore) {
            displayDettes(currentPage + 1);
        }
    };
}

// Initialiser l'affichage des clients pour la première page
displayDettes(currentPage);
