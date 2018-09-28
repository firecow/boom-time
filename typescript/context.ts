class Context {

    private loadingModal: LoadingModal;
    private errorModal: ErrorModal;

    constructor() {
        this.loadingModal = new LoadingModal(this.getElementById("loadingModal"));
        this.errorModal = new ErrorModal(this.getElementById("errorModal"), this.getElementById("errorMessage"));
    }

    getElementById<T>(id: string) {
        const element = document.getElementById<T>(id);
        if (element === null) {
            throw new Error(`Cannot find element for id: ${id}`);
        }
        return element;
    }

    workStarted() {
        this.loadingModal.show(true);
    }

    workStopped() {
        this.loadingModal.show(false);
    }

    error(err: Error) {
        this.errorModal.setErrorMessage(err);
        this.errorModal.showError(true);
    }
}