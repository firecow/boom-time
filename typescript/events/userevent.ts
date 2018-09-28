abstract class UserEvent {

    protected readonly ctx: Context;

    constructor(ctx: Context) {
        this.ctx = ctx;
    }

    public async run(event: Event) {
        event.preventDefault();
        this.ctx.workStarted();
        try {
            await this.doRun(event);
        } catch (e) {
            this.ctx.error(e);
        } finally {
            this.ctx.workStopped();
        }
    }

    protected abstract async doRun(event: Event): Promise<string>;

}
