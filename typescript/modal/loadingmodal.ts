class LoadingModal {

    private counter: number;
    private loadingModalElem: HTMLElement;

    constructor(loadingModalElem: HTMLElement) {
        this.counter = 0;
        this.loadingModalElem = loadingModalElem;
    }

    /**
     * Show/hide the loading dialog
     */
    show(show: boolean) {
        this.counter += show ? 1 : -1;
        this.update();
    }

    private update() {
        if (this.counter > 0) {
            this.loadingModalElem.classList.remove('hidden');
        } else {
            this.loadingModalElem.classList.add('hidden');
        }
    }
}
