// Global Modal System for CSCS SMS
function customConfirm(message, title = 'Confirm Action', type = 'question') {
    return new Promise((resolve) => {
        // Decode HTML entities and format message
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = message;
        const decodedMessage = tempDiv.textContent || tempDiv.innerText;
        
        // Split by newlines and format as list items
        const lines = decodedMessage.split('\n').filter(line => line.trim());
        const mainQuestion = lines[0];
        const details = lines.slice(1);
        
        let formattedMessage = `<p class="custom-modal-question">${mainQuestion}</p>`;
        if (details.length > 0) {
            formattedMessage += '<ul class="custom-modal-list">';
            details.forEach(line => {
                const cleanLine = line.trim().replace(/^[•\-]\s*/, '');
                if (cleanLine) {
                    formattedMessage += `<li>${cleanLine}</li>`;
                }
            });
            formattedMessage += '</ul>';
        }
        
        const modalHTML = `
            <div class="custom-modal-overlay" id="customModal">
                <div class="custom-modal-container">
                    <div class="custom-modal-header">
                        <h3 class="custom-modal-title">${title}</h3>
                    </div>
                    <div class="custom-modal-body">
                        ${formattedMessage}
                    </div>
                    <div class="custom-modal-footer">
                        <button class="custom-modal-btn custom-modal-btn-cancel" onclick="closeCustomModal(false)">Cancel</button>
                        <button class="custom-modal-btn custom-modal-btn-confirm" onclick="closeCustomModal(true)">Confirm</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        window.closeCustomModal = function(result) {
            const modal = document.getElementById('customModal');
            if (modal) {
                modal.remove();
            }
            resolve(result);
        };
    });
}

function customAlert(message, title = 'Alert', type = 'info') {
    return new Promise((resolve) => {
        // Decode HTML entities and format message
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = message;
        const decodedMessage = tempDiv.textContent || tempDiv.innerText;
        
        // Split by newlines and format
        const lines = decodedMessage.split('\n').filter(line => line.trim());
        const mainMessage = lines[0];
        const details = lines.slice(1);
        
        let formattedMessage = `<p class="custom-modal-question">${mainMessage}</p>`;
        if (details.length > 0) {
            formattedMessage += '<ul class="custom-modal-list">';
            details.forEach(line => {
                const cleanLine = line.trim().replace(/^[•\-]\s*/, '');
                if (cleanLine) {
                    formattedMessage += `<li>${cleanLine}</li>`;
                }
            });
            formattedMessage += '</ul>';
        }
        
        const modalHTML = `
            <div class="custom-modal-overlay" id="customModal">
                <div class="custom-modal-container">
                    <div class="custom-modal-header">
                        <h3 class="custom-modal-title">${title}</h3>
                    </div>
                    <div class="custom-modal-body">
                        ${formattedMessage}
                    </div>
                    <div class="custom-modal-footer">
                        <button class="custom-modal-btn custom-modal-btn-confirm" onclick="closeCustomAlert()">OK</button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        
        window.closeCustomAlert = function() {
            const modal = document.getElementById('customModal');
            if (modal) {
                modal.remove();
            }
            resolve(true);
        };
    });
}

// Override native alert and confirm
window.alert = function(message) {
    customAlert(message);
};

window.confirm = function(message) {
    return customConfirm(message);
};

