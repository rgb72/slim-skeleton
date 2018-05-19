const path = require('path')

const parseEntry = (object, path = null) => {
    let entry = {}

    let recursive = (object, current) => {
        for(let key in object) {
            let value = object[key]
            let newKey = current ? current + '/' + key : key
            if (value && typeof value === 'object') recursive(value, newKey)
            else entry[newKey] = (path || '') + '/' + value
        }
    }

    recursive(object)

    return entry
}

const resolve = dir => {
    return path.join(__dirname, dir)
}

module.exports = { parseEntry, resolve }