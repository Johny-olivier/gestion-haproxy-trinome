
const API = {
    baseUrl: '/api',

    async request(endpoint, method = 'GET', body = null) {
        const options = {
            method,
            headers: {
                'Accept': 'application/json'
            }
        };

        if (body !== null) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        try {
            const response = await fetch(`${this.baseUrl}${endpoint}`, options);

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Une erreur est survenue.');
            }

            return result;

        } catch (error) {
            console.error('Erreur API :', error);
            throw error;
        }
    },

    get(endpoint) {
        return this.request(endpoint, 'GET');
    },

    post(endpoint, body = {}) {
        return this.request(endpoint, 'POST', body);
    },

    put(endpoint, body = {}) {
        return this.request(endpoint, 'PUT', body);
    },

    delete(endpoint) {
        return this.request(endpoint, 'DELETE');
    }
};