class Ajax {

    private readonly url: string;

    constructor(url: string) {
        this.url = url;
    }

    async postFormData(formData: FormData): Promise<string> {
        const url = this.url;
        return new Promise<string>((resolve, reject) => {
            const xmlHttpRequest = this.setupXmlHttpRequest(resolve, reject);
            xmlHttpRequest.open('POST', url, true);
            xmlHttpRequest.send(formData);
        });
    }

    async getText(): Promise<string> {
        const url = this.url;
        return new Promise<string>((resolve, reject) => {
            const xmlHttpRequest = this.setupXmlHttpRequest(resolve, reject);
            xmlHttpRequest.open('GET', url, true);
            xmlHttpRequest.send();
        });
    }

    setupXmlHttpRequest(resolve: (value: string) => void, reject: (reason: string) => void): XMLHttpRequest {
        const xmlHttpRequest = new XMLHttpRequest();
        xmlHttpRequest.addEventListener('load', () => {
            if (xmlHttpRequest.status === 200) {
                resolve(xmlHttpRequest.responseText);
            } else {
                reject(`${xmlHttpRequest.status} ${xmlHttpRequest.responseText}`);
            }
        });
        xmlHttpRequest.addEventListener('error', () => {
            reject(`Error ${xmlHttpRequest.status}`);
        });
        return xmlHttpRequest;
    }
}
