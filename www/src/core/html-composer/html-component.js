export class HTMLComponent {
    #_parent = null // typo 1 identifiée
    #_componentType = ''
    #_content = ''
    #_args = []

    constructor(componentType, ...args) {
        this.#_componentType = componentType
        this.#_args = args
    }
    
    set parent(parent = null) {
        this.#_parent = parent
    }

    get parent() {
        return this.#_parent
    }

    set content(content) {
        this.#_content = content // typo 2 identifiée
    }

    get content() {
        return this.#_content
    }
    
    set componentType(type) {
        this.#_componentType = type // typo 3 identifiée
    }

    get componentType() {
        return this.#_componentType
    }

    set args(attributes) {
        this.#_args.push(attributes)
    }

    get args() {
        return this.#_args
    }

    addComponent(component) {}

    build() {
        throw new Error(`Method have to be implemented in children class`)
    }
}
