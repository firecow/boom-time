/// <reference path="userevent.ts"/>
class ErrorModalClick extends UserEvent {

    protected async doRun(): Promise<string> {
        const origin = window.location.origin;
        window.location.replace(origin);
        return Promise.resolve("Hidden error dialog");
    }

}
