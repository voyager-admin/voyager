class Fetch {
    get(url, parameters = {}, responseType = 'text') {
        return this.createRequest(url, 'get', parameters, responseType);
    }

    post(url, parameters = {}, responseType = 'text') {
        return this.createRequest(url, 'post', parameters, responseType);
    }

    put(url, parameters = {}, responseType = 'text') {
        return this.createRequest(url, 'put', parameters, responseType);
    }

    patch(url, parameters = {}, responseType = 'text') {
        return this.createRequest(url, 'patch', parameters, responseType);
    }

    delete(url, parameters = {}, responseType = 'text') {
        return this.createRequest(url, 'delete', parameters, responseType);
    }

    createRequest(url, method = 'post', parameters = {}, responseType = 'text') {
        var body = null;

        if (method.toLocaleLowerCase() !== 'get') {
            body = (parameters instanceof FormData ? parameters : JSON.stringify(parameters));
        }

        const config = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.head.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json, text/plain, */*'
            },
            body: body,
        };

        if (parameters instanceof FormData) {
            delete config.headers['Content-Type'];
        }

        return window
        .fetch(url, config)
        .then(async response => {
            if (response.ok && responseType.toLowerCase() === 'blob') {
                response.data = await response.blob();
            } else {
                response.data = await response.text();
            }
            return response;
        })
        .then((response) => {
            try {
                var json = JSON.parse(response.data);
                response.data = json;
            } catch { }

            if (response.ok) {
                return response;
            }
            
            return Promise.reject(response);
        });
    }
};

export default new Fetch();