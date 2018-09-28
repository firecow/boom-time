class SubmitSignup extends UserEvent {

    protected async doRun(event: Event): Promise<string> {
        const formElement = <HTMLFormElement>event.target;
        const formData = new FormData(formElement);
        const passwordElement: HTMLInputElement = this.ctx.getElementById<HTMLInputElement>("password");
        const passwordRepeatElement: HTMLInputElement = this.ctx.getElementById<HTMLInputElement>("password-repeat");

        if (passwordElement.value != passwordRepeatElement.value) {
            return Promise.reject(new Error("Password does not match"));
        }

        const ajax = new Ajax('/rest/signup/');
        const response = await ajax.postFormData(formData);
        window.location.reload();
        return response;
    }

}
