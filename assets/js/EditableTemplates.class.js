'use strict';

class EditableTemplates
{
    /**
     * @returns {string}
     */
    static getButtonTemplate() {
        return `<button type="submit" class="btn btn-primary btn-sm btn-primary-sham ml-0">
                    <i class="fas fa-check"></i>
                </button>    
                <button type="button" class="btn btn-secondary btn-sm editable-cancel ml-0">
                    <i class="fa fa-times"></i>
                </button>`;
    }

    /**
     * @param position
     * @returns {string}
     */
    static getFormTemplate(position = 'text-left') {
        return `<form class="form-inline editableform">
                    <div class="row w-100 p-0 m-1">
                        <div class="col-12 p-0 editable-input"></div>
                        <div class="col-12 editable-buttons pt-1 text-nowrap ${position}"></div>
                        <div class="editable-error-block"></div>
                    </div>
                </form>`;
    }
}

export { EditableTemplates };