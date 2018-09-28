class Pages {

    private readonly ctx: Context;
    private readonly subPageElem: HTMLElement;

    constructor(ctx: Context) {
        this.ctx = ctx;
        this.subPageElem = ctx.getElementById('subPage');
    }

    private async loadPageHtml(pageKey: string): Promise<string> {
        const ajax = new Ajax(`/rest/getpagehtml/?pageKey=${pageKey}`);
        this.ctx.workStarted();
        try {
            return await ajax.getText();
        } finally {
            this.ctx.workStopped();
        }
    }

    async pushPageToHistory(pageKey: string) {
        this.subPageElem.innerHTML = await this.loadPageHtml(pageKey);
        window.history.pushState(pageKey, `simple-php - ${pageKey}`, pageKey);
        console.info('Sub Page Pushed', pageKey);
    }

    async popPageFromHistory(pageKey: string) {
        this.subPageElem.innerHTML = await this.loadPageHtml(pageKey);
        console.info('Sub Page Poppped', pageKey);
    }

}
