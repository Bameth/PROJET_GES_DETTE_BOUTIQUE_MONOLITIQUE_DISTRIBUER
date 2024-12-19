document.addEventListener("DOMContentLoaded", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const clientId = urlParams.get('client_id');
    if (clientId) {
        displayClientDettes(clientId);
        displayClientInfo(clientId);
    } else {
        console.error("Aucun client_id trouvé dans l'URL.");
    }

    // Fonction pour afficher les informations du client
    function displayClientInfo(clientId) {
        const infoSection = document.getElementById("client-info");

        // Ajouter une animation de chargement
        infoSection.innerHTML = `<p>Chargement des informations du client...</p>`;

        fetch(`/api/client/${clientId}/dettes`)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la récupération des informations du client");
                }
                return response.json();
            })
            .then((data) => {
                const client = data.client;
                infoSection.innerHTML = `
                    <div class="flex items-center space-x-6">
                        <div class="p-4 bg-white rounded-full shadow-lg">
                            <svg class="h-16 w-16 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16.07 13a4 4 0 00-7.04 0m7.04 0A8 8 0 018.93 21m7.14 0A8 8 0 015.93 13m10.14 0a4 4 0 00-7.04 0M16.07 13A8 8 0 015.93 21m7.14 0A8 8 0 0116.07 13m-4-6a4 4 0 118 0 4 4 0 01-8 0z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-white text-4xl font-semibold">${client.nom} ${client.prenom}</h2>
                            <p class="text-indigo-100 text-lg mt-2">${client.telephone}</p>
                        </div>
                    </div>
                    <div class="mt-6 grid grid-cols-2 gap-4 text-white text-lg">
                        <div class="flex items-center justify-end space-x-2">
                            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V5a4 4 0 00-8 0v4M3 13h18m-9 0v8m0 0l-3-3m3 3l3-3" />
                            </svg>
                            <span class="font-bold text-xl">Montant dû : ${client.montantDue} FCFA</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="h-6 w-6 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-lg">${client.surname}</span>
                        </div>
                    </div>
                `;
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des informations du client :", error);
                infoSection.innerHTML = `<p class="text-red-500">Erreur de chargement des informations du client.</p>`;
            });
    }

    // Fonction pour afficher les dettes du client
    function displayClientDettes(clientId) {
        const list = document.getElementById("dette-list");

        // Ajouter une animation de chargement
        list.innerHTML = `
            <tr>
                <td colspan="6" class="text-center">Chargement des dettes...</td>
            </tr>
        `;
        // Ajouter une animation si les dettes sont vides
        if (list.innerHTML === "") {
            list.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">Aucune dette pour le moment</td>
                </tr>
            `;
        }

        const statusFilter = document.getElementById("status").value;
        const url = statusFilter ? `/api/client/${clientId}/dettes?status=${statusFilter}` : `/api/client/${clientId}/dettes`;

        fetch(url)
            .then((response) => {
                if (!response.ok) {
                    throw new Error("Erreur lors de la récupération des dettes");
                }
                return response.json();
            })
            .then((data) => {
                list.innerHTML = "";
                data.dettesClient.forEach((dette) => {
                    const row = document.createElement("tr");
                    row.innerHTML = `
                        <td class="px-6 py-4 text-center">${dette.date}</td>
                        <td class="px-6 py-4 text-center">${dette.montant} FCFA</td>
                        <td class="px-6 py-4 text-center">${dette.montantVerse} FCFA</td>
                        <td class="px-6 py-4 text-center">${dette.montantDue} FCFA</td>
                        <td class="px-6 py-4 text-center">${dette.statut}</td>
                        <td class="px-6 py-4 text-center">
                            <button class="text-red-600" onclick="confirmDeleteDette(${dette.id})">Supprimer</button>
                        </td>
                    `;
                    list.appendChild(row);
                });
            })
            .catch((error) => {
                console.error("Erreur lors de la récupération des dettes :", error);
                list.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-red-500">Erreur de chargement des dettes</td>
                    </tr>
                `;
            });
    }

    // Fonction pour confirmer la suppression d'une dette
    function confirmDeleteDette(detteId) {
        if (confirm("Êtes-vous sûr de vouloir supprimer cette dette ?")) {
            deleteDette(detteId);
        }
    }

    // Fonction pour supprimer une dette
    function deleteDette(detteId) {
        fetch(`/api/dette/${detteId}`, {
            method: 'DELETE',
        })
            .then((response) => {
                if (response.ok) {
                    alert("Dette supprimée avec succès.");
                    displayClientDettes(clientId);
                } else {
                    alert("Erreur lors de la suppression de la dette.");
                }
            })
            .catch((error) => {
                console.error("Erreur lors de la suppression de la dette :", error);
                alert("Erreur de suppression de la dette.");
            });
    }

    // Filtrer les dettes par statut
    const filterForm = document.getElementById("filter-form");
    filterForm.addEventListener("submit", function (event) {
        event.preventDefault();
        displayClientDettes(clientId);
    });
});
