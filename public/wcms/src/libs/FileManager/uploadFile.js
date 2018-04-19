class UploadFile {
    constructor (http, url) {
        this.http = http
        this.url = url
    }

    send (file, options = {}) {
        if(option.multipart) this.sendMultipartFile(file, option)
    }

    sendMultipartFile (file, options = {}) {
        let _options = {
            sliceSize: 256
        }

        Object.assign(_options, options)

        this.sendChuckFile(file, 0, _options.sliceSize)
    }

    sliceFile (file, start, end) {
        let slicer = file.mozSlice ? file.mozSlice :
                    file.webkitSlice ? file.webkitSlice :
                    file.slice ? file.slice : null

        if(slicer === null) return

        return slicer.bind(file)(start, end)
    }

    sendChuckFile (file, start, sliceSize, currentPath = 0) {
        let end = start + sliceSize
        if(end > file.size) end = file.size

        let sliced = this.sliceFile(file, start, end)
        ++currentPath

        let formData = new FormData()

        formData.append('multipart', true)
        formData.append('filename', file.name)
        formData.append('total_size', file.size)
        formData.append('part_size', sliceSize)
        formData.append('total_parts', this.totalParts(file.size, sliceSize))
        formData.append('current_part', currentPath)
        formData.append('start', start)
        formData.append('end', end)
        formData.append('file', sliced)

        this.http.post(this.url, formData).then(response => {
            if(end < file.size) {
                start = end
                this.sendChuckFile(file, start, sliceSize, currentPath)
            }
        }).catch(response => {
            // alert(response)
        })
    }

    totalParts(size, sliceSize) {
        return Math.ceil(size/sliceSize)
    }

}

export default UploadFile
