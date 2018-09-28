class SubmitProfileImage extends UserEvent {

    protected async doRun(event: Event): Promise<string> {
        const formElement = <HTMLFormElement>event.target;
        const formData = new FormData(formElement);

        const ajax = new Ajax('/rest/uploadprofileimage/');
        return await ajax.postFormData(formData);
    }

}
