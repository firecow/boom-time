class Modals {

    private readonly ctx: Context;
    private readonly modalElem: HTMLElement;

    constructor(ctx: Context) {
        this.ctx = ctx;
        this.modalElem = ctx.getElementById('modal');
    }

    private async loadModalHtml(modalKey: string): Promise<string> {
        const ajax = new Ajax(`/rest/getmodalhtml/?modalKey=${modalKey}`);
        this.ctx.workStarted();
        try {
            return await ajax.getText();
        } finally {
            this.ctx.workStopped();
        }
    }

    async openModal(modalKey: string) {
        this.modalElem.innerHTML = await this.loadModalHtml(modalKey);
        this.modalElem.classList.remove("hidden");
        console.info('Modal Opened', modalKey);
    }
}
