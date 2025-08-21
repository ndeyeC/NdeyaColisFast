// State management for zone and token selection
let selectedZoneId = '';
let selectedTokens = 0;
let tokenPrice = 0;

// DOM elements cached for performance
const elements = {
    zoneIdInput: document.getElementById('zoneIdInput'),
    selectedZoneContainer: document.getElementById('selectedZoneContainer'),
    selectedZoneName: document.getElementById('selectedZoneName'),
    selectedZonePrice: document.getElementById('selectedZonePrice'),
    customAmount: document.getElementById('customAmount'),
    tokenAmountInput: document.getElementById('tokenAmountInput'),
    selectedQuantityDisplay: document.getElementById('selectedQuantityDisplay'),
    selectedAmountFcfa: document.getElementById('selectedAmountFcfa'),
    selectionContainer: document.getElementById('selectionContainer'),
    submitButton: document.getElementById('submitButton'),
    tokenForm: document.getElementById('tokenForm')
};

/**
 * Selects a zone and updates the UI
 * @param {string} zoneId - ID of the selected zone
 * @param {string} zoneName - Name of the selected zone
 * @param {number} price - Price per token
 */
function selectZone(zoneId, zoneName, price) {
    selectedZoneId = zoneId;
    tokenPrice = price;

    elements.zoneIdInput.value = zoneId;
    elements.selectedZoneName.textContent = zoneName;
    elements.selectedZonePrice.textContent = price.toLocaleString('fr-FR');
    elements.selectedZoneContainer.classList.remove('hidden');

    clearSelection();
}

/**
 * Clears the selected zone and resets related UI
 */
function clearZoneSelection() {
    selectedZoneId = '';
    elements.zoneIdInput.value = '';
    elements.selectedZoneContainer.classList.add('hidden');
    clearSelection();
}

/**
 * Selects a predefined token amount and updates UI
 * @param {number} amount - Number of tokens
 * @param {HTMLElement} element - Clicked button element
 */
function selectTokenAmount(amount, element) {
    if (!selectedZoneId) {
        showError('Veuillez sélectionner une zone d’abord.');
        return;
    }

    elements.customAmount.value = '';
    selectedTokens = amount;
    updateSelectionDisplay(amount, amount * tokenPrice);

    document.querySelectorAll('.token-option').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'border-blue-400');
    });
    element.classList.add('bg-blue-100', 'border-blue-400');
}

/**
 * Applies a custom token amount entered by the user
 */
function applyCustomAmount() {
    if (!selectedZoneId) {
        showError('Veuillez sélectionner une zone d’abord.');
        return;
    }

    const amount = parseInt(elements.customAmount.value);
    if (isNaN(amount) || amount < 1 || amount > 50) {
        showError('Veuillez entrer une quantité valide (1 à 50).');
        return;
    }

    selectedTokens = amount;
    updateSelectionDisplay(amount, amount * tokenPrice);

    document.querySelectorAll('.token-option').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'border-blue-400');
    });
}

/**
 * Updates the selection display with the chosen quantity and total price
 * @param {number} quantity - Number of tokens
 * @param {number} total - Total price in FCFA
 */
function updateSelectionDisplay(quantity, total) {
    elements.tokenAmountInput.value = quantity;
    elements.selectedQuantityDisplay.textContent = quantity;
    elements.selectedAmountFcfa.textContent = total.toLocaleString('fr-FR');
    elements.selectionContainer.classList.remove('hidden');
    elements.submitButton.disabled = false;
}

/**
 * Clears the token selection and resets UI
 */
function clearSelection() {
    selectedTokens = 0;
    elements.tokenAmountInput.value = '';
    elements.customAmount.value = '';
    elements.selectionContainer.classList.add('hidden');
    elements.submitButton.disabled = true;

    document.querySelectorAll('.token-option').forEach(btn => {
        btn.classList.remove('bg-blue-100', 'border-blue-400');
    });
}

/**
 * Displays an error message to the user
 * @param {string} message - Error message to display
 */
function showError(message) {
    alert(message); // Replace with a more sophisticated notification system in production
}

// Form submission validation
elements.tokenForm.addEventListener('submit', (e) => {
    if (!selectedZoneId || !selectedTokens) {
        e.preventDefault();
        showError('Veuillez sélectionner une zone et une quantité de jetons.');
    }
});