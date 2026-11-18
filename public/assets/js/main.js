const App = {

    notify(message, type = 'success', duration = 3000) {
        const notification = document.createElement('div');

        notification.className = `notification notification-${type}`;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, duration);
    },

    confirm(message) {
        return window.confirm(message);
    },

    /**
     * Formate un état UP/DOWN.
     * reçoit le status - État du serveur.
     * retourne la Classe CSS correspondante.
     */
    statusClass(status) {
        const normalizedStatus = String(status).toUpperCase();

        if (normalizedStatus === 'UP') {
            return 'status-up';
        }

        if (normalizedStatus === 'DOWN') {
            return 'status-down';
        }

        return 'status-unknown';
    },
    
    formatDate(date) {
        return new Date(date).toLocaleString('fr-FR');
    }
};