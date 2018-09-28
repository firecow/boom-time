class LogoutClick extends UserEvent {

    protected async doRun(): Promise<string> {
        const ajax = new Ajax('/rest/logout/');
        const response = await ajax.postFormData(new FormData());
        const origin = window.location.origin;
        window.location.replace(origin);
        return response;
    }

}
