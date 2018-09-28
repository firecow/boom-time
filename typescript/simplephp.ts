class SimplePHP {

    private readonly pages: Pages;
    private readonly modals: Modals;
    private readonly ctx: Context;

    public clickLogout: UserEvent;
    public clickErrorModal: UserEvent;

    public submitLogin: UserEvent;
    public submitSignup: UserEvent;
    public submitProfileImage: UserEvent;


    constructor(ctx: Context) {

        this.ctx = ctx;

        this.pages = new Pages(ctx);
        this.modals = new Modals(ctx);

        this.clickLogout = new LogoutClick(ctx);
        this.clickErrorModal = new ErrorModalClick(ctx);
        this.submitLogin = new SubmitLogin(ctx);
        this.submitSignup = new SubmitSignup(ctx);
        this.submitProfileImage = new SubmitProfileImage(ctx);

        // Someone have popped the state.
        window.addEventListener('popstate', (e) => {
            let state = e.state;
            if (state == null) {
                state = "/";
            }
            return this.pages.popPageFromHistory(state);
        });
    }

    openModal(modalKey: string) {
        this.modals.openModal(modalKey).catch((e) => {
            this.ctx.error(e);
        });
    }

    pushPageToHistory(pageKey: string) {
        this.pages.pushPageToHistory(pageKey).catch((e) => {
            this.ctx.error(e);
        });
    }

}

// The browser window is ready
window.addEventListener('load', () => {
    const ctx = new Context();
    window.simplePHP = new SimplePHP(ctx);
    console.log("Page loaded");
});
