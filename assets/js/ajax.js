const Ajax = {
    request: async (url, method = 'GET', data = null) => {
        const options = { method, headers: { 'Content-Type': 'application/json' } };
        if (data) {
            options.body = JSON.stringify(data);
        }
        const response = await fetch(url, options);
        return response.json();
    },
    get: async (url) => Ajax.request(url, 'GET'),
    post: async (url, data) => Ajax.request(url, 'POST', data),
    put: async (url, data) => Ajax.request(url, 'PUT', data),
    delete: async (url, data) => Ajax.request(url, 'DELETE', data),
};
