class ErrorModal {

    private errorModelElem: HTMLElement;
    private errorMsgElem: HTMLElement;

    constructor(errorModalElem: HTMLElement, errorMsgElem: HTMLElement) {
        this.errorModelElem = errorModalElem;
        this.errorMsgElem = errorMsgElem;
    }

    setErrorMessage(err: Error) {
        this.errorMsgElem.innerHTML = `${err}`;
    }

    showError(show: boolean) {
        if (show) {
            this.errorModelElem.classList.remove('hidden');
        } else {
            this.errorModelElem.classList.add('hidden');
        }
    }
}
