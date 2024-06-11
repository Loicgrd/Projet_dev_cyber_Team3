import { of } from 'rxjs'
import { HttpClient } from '../core/http-client/http-client'
export class LoginService {
    #protocol = 'http'
    #apiHost = 'localhost'
    #apiPort = 8003
    #apiEndpoint = 'signin'

    #uri = ''

    #httpClient = null

    constructor() {
        this.#httpClient = new HttpClient()
        this.#uri = `${this.#protocol}://${this.#apiHost}:${this.#apiPort}/`
    }

    signin(value) {

        const uri = `${this.#uri}${this.#apiEndpoint}`
        return this.#httpClient.post(
            uri,
            value
        )
    }

    storeToken(token) {
        localStorage.setItem('jwt', token)
    }
}