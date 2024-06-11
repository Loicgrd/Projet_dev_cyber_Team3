import { from, map, Observable, of, throwError } from "rxjs"

export class HttpClient {
    #_method  = 'GET'
    #_uri = ''
    #_contentType = 'application/json'
    #_body = ''
    #_fetchOptions = {}
    #_token = localStorage.getItem('token');
    /**
     * 
     * @param {string} uri 
     * @returns Observable<any>
     */
    get(uri) {
        this.#_uri = uri

        this.#_fetchOptions = {
            method: 'get',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType,
            }
        }
        if (this.#_token) {
            this.#_fetchOptions.headers["Authorization"] = `Bearer ${this.#_token}`;
        }
        return this.#send()

    }

    /**
     * 
     * @param {string} uri 
     * @param {any} body 
     * @returns Observable<any>
     */
    post(uri, body) {
        this.#_method = 'post'
        this.#_uri = uri
        this.#_body = JSON.stringify(body)

        this.#_fetchOptions = {
            method: 'post',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType,
            },
            body: this.#_body
        }
        if (this.#_token) {
            this.#_fetchOptions.headers["Authorization"] = `Bearer ${this.#_token}`;
        }
        return this.#send()
        
    }

    /**
     * 
     * @param {string} uri 
     * @param {any} body 
     * @returns Observable<any>
     */
    put(uri, body) {
        this.#_method = 'put'
        this.#_uri = uri
        
        this.#_body = JSON.stringify(body)

        this.#_fetchOptions = {
            method: 'put',
            mode: 'cors',
            headers: {
                "Content-Type": this.#_contentType,
            },
            body: this.#_body
        }
        if (this.#_token) {
            this.#_fetchOptions.headers["Authorization"] = `Bearer ${this.#_token}`;
        }
        return this.#send()        
    }

   #send() {
        const apiCall = fetch(
            this.#_uri,
            this.#_fetchOptions
        )
        .then((response) => {
            if (response.ok) {
                return response.json()
            }
            throw new Error(`Something went wrong calling ${this.#_method.toUpperCase()} ${this.#_uri} (${JSON.stringify(response)})`)
        })
        .then((responseJson) => responseJson)

        return from(apiCall)
    }
}
