class SubmitLogin extends UserEvent {

    protected async doRun(event: Event): Promise<string> {
        const formElement = <HTMLFormElement>event.target;
        const formData = new FormData(formElement);

        const ajax = new Ajax('/rest/login/');
        const response = await ajax.postFormData(formData);
        window.location.reload();
        return response;
    }

}
